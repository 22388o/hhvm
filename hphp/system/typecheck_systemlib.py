#!/usr/bin/env python3

# Gather all of the relevant files from buck file groups and execute
# `hh_single_type_check` with the correct flags

import argparse
import os
import subprocess as p
from typing import List

FLAGS: List[str] = [
    "--no-builtins",
    "--enable-systemlib-annotations",
]


def get_files_in(path: str) -> List[str]:
    all_files = []
    for root, _, files in os.walk(path):
        all_files.extend(os.path.join(root, f) for f in files)
    return all_files


def main():
    parser = argparse.ArgumentParser(
        description="Gather PHP files in given directories and run `hh_single_type_check`"
    )
    parser.add_argument("paths", type=str, help="paths to traverse", nargs="+")
    parser.add_argument("--hhstc-path", type=str, help="`hh_single_type_check` to run")
    args = parser.parse_args()
    files = []
    for path in args.paths:
        files.extend(get_files_in(path))
    p.run([args.hhstc_path] + FLAGS + files, check=True)


if __name__ == "__main__":
    main()
