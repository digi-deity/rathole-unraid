#!/bin/bash
DIR="$(dirname "$(readlink -f ${BASH_SOURCE[0]})")"
tmpdir=/tmp/tmp.$(( $RANDOM * 19318203981230 + 40 ))
plugin=$(basename ${DIR})
archive="$(dirname $(dirname ${DIR}))/archive"
version=$(date +"%Y.%m.%d")$1

mkdir -p $tmpdir

cd $DIR
cp --parents -f $(find . -type f ! \( -iname "pkg_build.sh" \) ) $tmpdir/
cd $tmpdir
find . -type f \( -iname '*.sh' -o -iname '*.page' -o -iname '*.plg' -o -iname 'rc.*' \) -exec sed -i 's/\r//g' {} +
chmod -R +x ./
tar cfJCo ${archive}/${plugin}-${version}-x86_64-1.txz $tmpdir . --owner=0 --group=0 --mode="a=r,u+w,a+X"
rm -rf $tmpdir
md5sum ${archive}/${plugin}-${version}-x86_64-1.txz
