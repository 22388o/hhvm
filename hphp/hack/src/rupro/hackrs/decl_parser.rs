// Copyright (c) Meta Platforms, Inc. and affiliates.
//
// This source code is licensed under the MIT license found in the
// LICENSE file in the "hack" directory of this source tree.

use crate::special_names;
use arena_collections::list::List;
use names::FileSummary;
use obr::{
    decl_parser_options::DeclParserOptions,
    direct_decl_parser::Decls,
    parser_options::ParserOptions,
    shallow_decl_defs::{Decl, ShallowClass},
};
use oxidized_by_ref as obr;
use pos::{RelativePath, RelativePathCtx, TypeName};
use std::marker::PhantomData;
use std::sync::Arc;
use ty::decl::shallow;
use ty::reason::Reason;

mod options;
use options::Options;

#[derive(Debug, Clone)]
pub struct DeclParser<R: Reason> {
    relative_path_ctx: Arc<RelativePathCtx>,
    opts: Options,
    // We could make our parse methods generic over `R` instead, but it's
    // usually more convenient for callers (especially tests) to pin the decl
    // parser to a single Reason type.
    _phantom: PhantomData<R>,
}

impl<R: Reason> DeclParser<R> {
    pub fn new(relative_path_ctx: Arc<RelativePathCtx>) -> Self {
        Self {
            relative_path_ctx,
            opts: Default::default(),
            _phantom: PhantomData,
        }
    }

    pub fn with_options(relative_path_ctx: Arc<RelativePathCtx>, opts: &ParserOptions<'_>) -> Self {
        Self {
            relative_path_ctx,
            opts: Options::from(opts),
            _phantom: PhantomData,
        }
    }

    pub fn parse(&self, path: RelativePath) -> std::io::Result<Vec<shallow::Decl<R>>> {
        let arena = bumpalo::Bump::new();
        let absolute_path = path.to_absolute(&self.relative_path_ctx);
        let text = std::fs::read(&absolute_path)?;
        let decl_parser_opts = DeclParserOptions::from_parser_options(self.opts.get());
        let parsed_file = self.parse_impl(&decl_parser_opts, path, &text, &arena);
        Ok(parsed_file.decls.iter().map(Into::into).collect())
    }

    pub fn parse_and_summarize(
        &self,
        path: RelativePath,
    ) -> std::io::Result<(Vec<shallow::Decl<R>>, FileSummary)> {
        let arena = bumpalo::Bump::new();
        let absolute_path = path.to_absolute(&self.relative_path_ctx);
        let text = std::fs::read(&absolute_path)?;
        let opts = DeclParserOptions::from(self.opts.get());
        let parsed_file = self.parse_impl(&opts, path, &text, &arena);
        let summary = FileSummary::from_decls(parsed_file);
        Ok((parsed_file.decls.iter().map(Into::into).collect(), summary))
    }

    fn parse_impl<'a>(
        &self,
        opts: &'a DeclParserOptions<'a>,
        path: RelativePath,
        text: &'a [u8],
        arena: &'a bumpalo::Bump,
    ) -> oxidized_by_ref::direct_decl_parser::ParsedFile<'a> {
        let mut parsed_file = direct_decl_parser::parse_decls(opts, path.into(), text, arena);
        // TODO: The direct decl parser should return decls in the same
        // order as they are declared in the file. At the moment it reverses
        // them. Reverse them again to match the syntactic order.
        let parser_options = self.opts.get();
        let deregister_std_lib = path.is_hhi() && parser_options.po_deregister_php_stdlib;
        if deregister_std_lib {
            parsed_file.decls = Decls(List::rev_from_iter_in(
                (parsed_file.decls.iter()).filter_map(|d| remove_php_stdlib_decls(arena, d)),
                arena,
            ));
        } else {
            parsed_file.decls.rev(arena);
        }
        parsed_file
    }
}

// note(sf, 2022-04-12, c.f.
// `hphp/hack/src/providers/direct_decl_utils.ml`)
fn remove_php_stdlib_decls<'a>(
    arena: &'a bumpalo::Bump,
    (name, decl): (&'a str, Decl<'a>),
) -> Option<(&'a str, Decl<'a>)> {
    match decl {
        Decl::Fun(fun) if fun.php_std_lib => None,
        Decl::Class(class)
            if (class.user_attributes.iter()).any(|ua| {
                TypeName::new(ua.name.1) == *special_names::user_attributes::uaPHPStdLib
            }) =>
        {
            None
        }
        Decl::Class(class) => {
            let props = bumpalo::collections::Vec::from_iter_in(
                (class.props.iter())
                    .filter(|p| !p.flags.contains(obr::prop_flags::PropFlags::PHP_STD_LIB))
                    .copied(),
                arena,
            )
            .into_bump_slice();
            let sprops = bumpalo::collections::Vec::from_iter_in(
                (class.sprops.iter())
                    .filter(|p| !p.flags.contains(obr::prop_flags::PropFlags::PHP_STD_LIB))
                    .copied(),
                arena,
            )
            .into_bump_slice();
            let methods = bumpalo::collections::Vec::from_iter_in(
                (class.methods.iter())
                    .filter(|m| {
                        !m.flags
                            .contains(obr::method_flags::MethodFlags::PHP_STD_LIB)
                    })
                    .copied(),
                arena,
            )
            .into_bump_slice();
            let static_methods = bumpalo::collections::Vec::from_iter_in(
                (class.static_methods.iter())
                    .filter(|m| {
                        !m.flags
                            .contains(obr::method_flags::MethodFlags::PHP_STD_LIB)
                    })
                    .copied(),
                arena,
            )
            .into_bump_slice();
            let masked = arena.alloc(ShallowClass {
                props,
                sprops,
                methods,
                static_methods,
                ..*class
            });
            Some((name, Decl::Class(masked)))
        }
        _ => Some((name, decl)),
    }
}
