/*
   +----------------------------------------------------------------------+
   | HipHop for PHP                                                       |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010-present Facebook, Inc. (http://www.facebook.com)  |
   | Copyright (c) 1997-2010 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
*/

#pragma once

#include "hphp/runtime/ext/std/ext_std.h"
#include <signal.h>

namespace HPHP {
///////////////////////////////////////////////////////////////////////////////

Variant HHVM_FUNCTION(shell_exec,
                      const String& cmd);
String HHVM_FUNCTION(exec,
                     const String& command,
                     Array& output,
                     int64_t& return_var);
void HHVM_FUNCTION(passthru,
                   const String& command,
                   int64_t& return_var);
String HHVM_FUNCTION(system,
                     const String& command,
                     int64_t& return_var);

///////////////////////////////////////////////////////////////////////////////

Variant HHVM_FUNCTION(proc_open,
                      const String& cmd,
                      const Array& descriptorspec,
                      Array& pipes,
                      const Variant& cwd = uninit_variant,
                      const Variant& env = uninit_variant,
                      const Variant& other_options = uninit_variant);
bool HHVM_FUNCTION(proc_terminate,
                   const Resource& process,
                   int64_t signal = SIGTERM);
int64_t HHVM_FUNCTION(proc_close,
                      const Resource& process);
Array HHVM_FUNCTION(proc_get_status,
                    const Resource& process);
bool HHVM_FUNCTION(proc_nice,
                   int64_t increment);

///////////////////////////////////////////////////////////////////////////////

String HHVM_FUNCTION(escapeshellarg,
                     const String& arg);
String HHVM_FUNCTION(escapeshellcmd,
                     const String& command);

///////////////////////////////////////////////////////////////////////////////
}
