// Copyright (c) Facebook, Inc. and its affiliates.
//
// This source code is licensed under the MIT license found in the
// LICENSE file in the "hack" directory of this source tree.
//
// @generated SignedSource<<6d0133bbd23f2b5f81c6731237a7fc5f>>
//
// To regenerate this file, run:
//   hphp/hack/src/oxidized_regen.sh

use arena_trait::TrivialDrop;
use eq_modulo_pos::EqModuloPos;
use eq_modulo_pos::EqModuloPosAndReason;
use no_pos_hash::NoPosHash;
use ocamlrep_derive::FromOcamlRepIn;
use ocamlrep_derive::ToOcamlRep;
use serde::Deserialize;
use serde::Serialize;

#[allow(unused_imports)]
use crate::*;

#[derive(
    Clone,
    Debug,
    Deserialize,
    Eq,
    EqModuloPos,
    EqModuloPosAndReason,
    FromOcamlRepIn,
    Hash,
    NoPosHash,
    Ord,
    PartialEq,
    PartialOrd,
    Serialize,
    ToOcamlRep
)]
#[repr(C)]
pub struct TypingEnvReturnInfo<'a> {
    #[serde(deserialize_with = "arena_deserializer::arena", borrow)]
    pub type_: &'a typing_defs::PossiblyEnforcedTy<'a>,
    pub disposable: bool,
    pub explicit: bool,
    pub dynamically_callable: bool,
}
impl<'a> TrivialDrop for TypingEnvReturnInfo<'a> {}
arena_deserializer::impl_deserialize_in_arena!(TypingEnvReturnInfo<'arena>);
