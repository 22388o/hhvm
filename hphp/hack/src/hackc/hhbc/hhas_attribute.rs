// Copyright (c) Facebook, Inc. and its affiliates.
//
// This source code is licensed under the MIT license found in the
// LICENSE file in the "hack" directory of this source tree.

use crate::typed_value::TypedValue;
use ffi::{Slice, Str};
use naming_special_names::user_attributes as ua;
use naming_special_names_rust as naming_special_names;

/// Attributes with a name from [naming_special_names::user_attributes] and
/// a series of arguments.  Emitter code can match on an attribute as follows:
/// ```
///   use naming_special_names::user_attributes as ua;
///   fn is_memoized(attr: &HhasAttribute) -> bool {
///      attr.is(ua::memoized)
///   }
///   fn has_dynamically_callable(attrs: &Vec<HhasAttribute>) {
///       attrs.iter().any(|a| a.name == ua::DYNAMICALLY_CALLABLE)
///   }
/// ```

#[derive(Clone, Debug, Eq, PartialEq)]
#[repr(C)]
pub struct HhasAttribute<'arena> {
    pub name: Str<'arena>,
    pub arguments: Slice<'arena, TypedValue<'arena>>,
}

impl<'arena> HhasAttribute<'arena> {
    pub fn is<F: Fn(&str) -> bool>(&self, f: F) -> bool {
        f(self.name.unsafe_as_str())
    }
}

fn is<'arena>(s: &str, attr: &HhasAttribute<'arena>) -> bool {
    attr.is(|x| x == s)
}

fn has<'arena, F>(attrs: &[HhasAttribute<'arena>], f: F) -> bool
where
    F: Fn(&HhasAttribute<'arena>) -> bool,
{
    attrs.iter().any(f)
}

pub fn is_no_injection<'arena>(attrs: impl AsRef<[HhasAttribute<'arena>]>) -> bool {
    is_native_arg(native_arg::NO_INJECTION, attrs)
}

pub fn is_native_opcode_impl<'arena>(attrs: impl AsRef<[HhasAttribute<'arena>]>) -> bool {
    is_native_arg(native_arg::OP_CODE_IMPL, attrs)
}

fn is_native_arg<'arena>(s: &str, attrs: impl AsRef<[HhasAttribute<'arena>]>) -> bool {
    attrs.as_ref().iter().any(|attr| {
        attr.is(ua::is_native)
            && attr.arguments.as_ref().iter().any(|tv| match *tv {
                TypedValue::String(s0) => s0.unsafe_as_str() == s,
                _ => false,
            })
    })
}

fn is_foldable<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__IsFoldable", attr)
}

fn is_dynamically_constructible<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__DynamicallyConstructible", attr)
}

fn is_sealed<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__Sealed", attr)
}

fn is_const<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__Const", attr)
}

fn is_meth_caller<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__MethCaller", attr)
}

fn is_provenance_skip_frame<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__ProvenanceSkipFrame", attr)
}

fn is_dynamically_callable<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__DynamicallyCallable", attr)
}

fn is_native<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__Native", attr)
}

fn is_enum_class<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    is("__EnumClass", attr)
}

fn is_memoize<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    ua::is_memoized(attr.name.unsafe_as_str())
}

fn is_memoize_lsb<'arena>(attr: &HhasAttribute<'arena>) -> bool {
    attr.name.unsafe_as_str() == ua::MEMOIZE_LSB
        || attr.name.unsafe_as_str() == ua::POLICY_SHARDED_MEMOIZE_LSB
}

pub fn has_native<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_native)
}

pub fn has_enum_class<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_enum_class)
}

pub fn has_dynamically_constructible<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_dynamically_constructible)
}

pub fn has_foldable<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_foldable)
}

pub fn has_sealed<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_sealed)
}

pub fn has_const<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_const)
}

pub fn has_meth_caller<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_meth_caller)
}

pub fn has_provenance_skip_frame<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_provenance_skip_frame)
}

pub fn has_dynamically_callable<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_dynamically_callable)
}

pub fn has_is_memoize<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_memoize)
}

pub fn has_is_memoize_lsb<'arena>(attrs: &[HhasAttribute<'arena>]) -> bool {
    has(attrs, is_memoize_lsb)
}

pub fn deprecation_info<'a, 'arena>(
    mut iter: impl Iterator<Item = &'a HhasAttribute<'arena>>,
) -> Option<&'a [TypedValue<'arena>]> {
    iter.find_map(|attr| {
        if attr.name.unsafe_as_str() == ua::DEPRECATED {
            Some(attr.arguments.as_ref())
        } else {
            None
        }
    })
}

pub mod native_arg {
    pub const OP_CODE_IMPL: &str = "OpCodeImpl";
    pub const NO_INJECTION: &str = "NoInjection";
}

#[cfg(test)]
mod tests {
    use super::*;

    use naming_special_names::user_attributes as ua;

    #[test]
    fn example_is_memoized_vs_eq_memoize() {
        let attr = HhasAttribute {
            name: ua::MEMOIZE_LSB.into(),
            arguments: ffi::Slice::empty(),
        };
        assert!(attr.is(ua::is_memoized));
        assert!(!attr.is(|s| s == ua::MEMOIZE));
        assert!(attr.is(|s| s == ua::MEMOIZE_LSB));
    }

    #[test]
    fn example_has_dynamically_callable() {
        let mk_attr = |name: &'static str| HhasAttribute {
            name: name.into(),
            arguments: ffi::Slice::empty(),
        };
        let attrs = vec![mk_attr(ua::CONST), mk_attr(ua::DYNAMICALLY_CALLABLE)];
        let has_result = attrs
            .iter()
            .any(|a| a.name.unsafe_as_str() == ua::DYNAMICALLY_CALLABLE);
        assert!(has_result);
    }
}
