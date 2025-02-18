<?hh

class foobar {}
function foobar() {}
class StrObj {
  public function __construct(private string $s)[] {}
  public function __toString(): string { return $this->s; }
}
class Wrapper { public function __construct(private mixed $w)[] {} }

function LV($x)  { return __hhvm_intrinsics\launder_value($x); }
function CLS($c) { return __hhvm_intrinsics\create_class_pointer($c); }

function WRAPA($x) { return LV(varray[$x]); }
function WRAPO($x) { return LV(new Wrapper($x)); }
function WRAPD($x) { $r = new stdClass; $r->x = $x; return LV($r); }

<<__NEVER_INLINE>> function print_header($title) {
  echo "$title\n";
  echo "+------------+------+------+------+------+------+------+\n";
  echo "| VAR        | <    | <=   | >    | >=   | ==   | ===  |\n";
  echo "+============+======+======+======+======+======+======+";
}
<<__NEVER_INLINE>> function begin_row($var, $wrap = null) {
  printf("\n| %-10s |", $wrap !== null ? $wrap."(\$$var)" : "\$$var");
}
<<__NEVER_INLINE>> function C(bool $v) {
  printf(" %s    |", $v ? 'T' : 'F');
}
<<__NEVER_INLINE>> function print_footer() {
  echo "\n+------------+------+------+------+------+------+------+\n\n";
}

<<__NEVER_INLINE>> function static_compare() {
  $cm = foobar::class;
  $va = 'foobar';
  $oa = new StrObj('foobar');
  $fa = CLS('foobar');

  $xx = varray[$cm]; $vx = varray[$va]; $ox = varray[$oa]; $fx = varray[$fa];

  $xy = new Wrapper($cm); $vy = new Wrapper($va); $oy = new Wrapper($oa);
  $fy = new Wrapper($fa);

  $xz = new stdClass; $xz->v = $cm; $vz = new stdClass; $vz->v = $va;
  $oz = new stdClass; $oz->v = $oa; $fz = new stdClass; $fz->v = $fa;

  print_header('[static] $cm ? VAR');
  begin_row('va');
    C($cm<$va);C($cm<=$va);C($cm>$va);C($cm>=$va);C($cm==$va);C($cm===$va);
  begin_row('oa');
    C(HH\Lib\Legacy_FIXME\lt($cm, $oa));C(HH\Lib\Legacy_FIXME\lte($cm, $oa));C(HH\Lib\Legacy_FIXME\gt($cm, $oa));C(HH\Lib\Legacy_FIXME\gte($cm, $oa));C(HH\Lib\Legacy_FIXME\eq($cm, $oa));C($cm===$oa);
  begin_row('fa');
    C($cm<$fa);C($cm<=$fa);C($cm>$fa);C($cm>=$fa);C($cm==$fa);C($cm===$fa);

  begin_row('va', 'WRAPA');
    C($xx<$vx);C($xx<=$vx);C($xx>$vx);C($xx>=$vx);C($xx==$vx);C($xx===$vx);
  begin_row('oa', 'WRAPA');
    C(HH\Lib\Legacy_FIXME\lt($xx, $ox));C(HH\Lib\Legacy_FIXME\lte($xx, $ox));C(HH\Lib\Legacy_FIXME\gt($xx, $ox));C(HH\Lib\Legacy_FIXME\gte($xx, $ox));C(HH\Lib\Legacy_FIXME\eq($xx, $ox));C($xx===$ox);
  begin_row('fa', 'WRAPA');
    C($xx<$fx);C($xx<=$fx);C($xx>$fx);C($xx>=$fx);C($xx==$fx);C($xx===$fx);

  begin_row('va', 'WRAPO');
    C($xy<$vy);C($xy<=$vy);C($xy>$vy);C($xy>=$vy);C($xy==$vy);C($xy===$vy);
  begin_row('oa', 'WRAPO');
    C(HH\Lib\Legacy_FIXME\lt($xy, $oy));C(HH\Lib\Legacy_FIXME\lte($xy, $oy));C(HH\Lib\Legacy_FIXME\gt($xy, $oy));C(HH\Lib\Legacy_FIXME\gte($xy, $oy));C(HH\Lib\Legacy_FIXME\eq($xy, $oy));C($xy===$oy);
  begin_row('fa', 'WRAPO');
    C($xy<$fy);C($xy<=$fy);C($xy>$fy);C($xy>=$fy);C($xy==$fy);C($xy===$fy);

  begin_row('va', 'WRAPD');
    C($xz<$vz);C($xz<=$vz);C($xz>$vz);C($xz>=$vz);C($xz==$vz);C($xz===$vz);
  begin_row('oa', 'WRAPD');
    C(HH\Lib\Legacy_FIXME\lt($xz, $oz));C(HH\Lib\Legacy_FIXME\lte($xz, $oz));C(HH\Lib\Legacy_FIXME\gt($xz, $oz));C(HH\Lib\Legacy_FIXME\gte($xz, $oz));C(HH\Lib\Legacy_FIXME\eq($xz, $oz));C($xz===$oz);
  begin_row('fa', 'WRAPD');
    C($xz<$fz);C($xz<=$fz);C($xz>$fz);C($xz>=$fz);C($xz==$fz);C($xz===$fz);
  print_footer();

  print_header('[static] VAR ? $cm');
  begin_row('va');
    C($va<$cm);C($va<=$cm);C($va>$cm);C($va>=$cm);C($va==$cm);C($va===$cm);
  begin_row('oa');
    C(HH\Lib\Legacy_FIXME\lt($oa, $cm));C(HH\Lib\Legacy_FIXME\lte($oa, $cm));C(HH\Lib\Legacy_FIXME\gt($oa, $cm));C(HH\Lib\Legacy_FIXME\gte($oa, $cm));C(HH\Lib\Legacy_FIXME\eq($oa, $cm));C($oa===$cm);
  begin_row('fa');
    C($fa<$cm);C($fa<=$cm);C($fa>$cm);C($fa>=$cm);C($fa==$cm);C($fa===$cm);

  begin_row('va', 'WRAPA');
    C($vx<$xx);C($vx<=$xx);C($vx>$xx);C($vx>=$xx);C($vx==$xx);C($vx===$xx);
  begin_row('oa', 'WRAPA');
    C(HH\Lib\Legacy_FIXME\lt($ox, $xx));C(HH\Lib\Legacy_FIXME\lte($ox, $xx));C(HH\Lib\Legacy_FIXME\gt($ox, $xx));C(HH\Lib\Legacy_FIXME\gte($ox, $xx));C(HH\Lib\Legacy_FIXME\eq($ox, $xx));C($ox===$xx);
  begin_row('fa', 'WRAPA');
    C($fx<$xx);C($fx<=$xx);C($fx>$xx);C($fx>=$xx);C($fx==$xx);C($fx===$xx);

  begin_row('va', 'WRAPO');
    C($vy<$xy);C($vy<=$xy);C($vy>$xy);C($vy>=$xy);C($vy==$xy);C($vy===$xy);
  begin_row('oa', 'WRAPO');
    C(HH\Lib\Legacy_FIXME\lt($oy, $xy));C(HH\Lib\Legacy_FIXME\lte($oy, $xy));C(HH\Lib\Legacy_FIXME\gt($oy, $xy));C(HH\Lib\Legacy_FIXME\gte($oy, $xy));C(HH\Lib\Legacy_FIXME\eq($oy, $xy));C($oy===$xy);
  begin_row('fa', 'WRAPO');
    C($fy<$xy);C($fy<=$xy);C($fy>$xy);C($fy>=$xy);C($fy==$xy);C($fy===$xy);

  begin_row('va', 'WRAPD');
    C($vz<$xz);C($vz<=$xz);C($vz>$xz);C($vz>=$xz);C($vz==$xz);C($vz===$xz);
  begin_row('oa', 'WRAPD');
    C(HH\Lib\Legacy_FIXME\lt($oz, $xz));C(HH\Lib\Legacy_FIXME\lte($oz, $xz));C(HH\Lib\Legacy_FIXME\gt($oz, $xz));C(HH\Lib\Legacy_FIXME\gte($oz, $xz));C(HH\Lib\Legacy_FIXME\eq($oz, $xz));C($oz===$xz);
  begin_row('fa', 'WRAPD');
    C($fz<$xz);C($fz<=$xz);C($fz>$xz);C($fz>=$xz);C($fz==$xz);C($fz===$xz);
  print_footer();
}

<<__NEVER_INLINE>> function dynamic_compare() {
  $cm = LV(foobar::class);
  $va = LV('foobar');
  $oa = LV(new StrObj('foobar'));
  $fa = LV(CLS('foobar'));

  $xx = WRAPA($cm); $vx = WRAPA($va); $ox = WRAPA($oa); $fx = WRAPA($fa);

  $xy = WRAPO($cm); $vy = WRAPO($va); $oy = WRAPO($oa); $fy = WRAPO($fa);

  $xz = WRAPD($cm); $vz = WRAPD($va); $oz = WRAPD($oa); $fz = WRAPD($fa);

  print_header('[dynamic] $cm ? VAR');
  begin_row('va');
    C($cm<$va);C($cm<=$va);C($cm>$va);C($cm>=$va);C($cm==$va);C($cm===$va);
  begin_row('oa');
    C(HH\Lib\Legacy_FIXME\lt($cm, $oa));C(HH\Lib\Legacy_FIXME\lte($cm, $oa));C(HH\Lib\Legacy_FIXME\gt($cm, $oa));C(HH\Lib\Legacy_FIXME\gte($cm, $oa));C(HH\Lib\Legacy_FIXME\eq($cm, $oa));C($cm===$oa);
  begin_row('fa');
    C($cm<$fa);C($cm<=$fa);C($cm>$fa);C($cm>=$fa);C($cm==$fa);C($cm===$fa);

  begin_row('va', 'WRAPA');
    C($xx<$vx);C($xx<=$vx);C($xx>$vx);C($xx>=$vx);C($xx==$vx);C($xx===$vx);
  begin_row('oa', 'WRAPA');
    C(HH\Lib\Legacy_FIXME\lt($xx, $ox));C(HH\Lib\Legacy_FIXME\lte($xx, $ox));C(HH\Lib\Legacy_FIXME\gt($xx, $ox));C(HH\Lib\Legacy_FIXME\gte($xx, $ox));C(HH\Lib\Legacy_FIXME\eq($xx, $ox));C($xx===$ox);
  begin_row('fa', 'WRAPA');
    C($xx<$fx);C($xx<=$fx);C($xx>$fx);C($xx>=$fx);C($xx==$fx);C($xx===$fx);

  begin_row('va', 'WRAPO');
    C($xy<$vy);C($xy<=$vy);C($xy>$vy);C($xy>=$vy);C($xy==$vy);C($xy===$vy);
  begin_row('oa', 'WRAPO');
    C(HH\Lib\Legacy_FIXME\lt($xy, $oy));C(HH\Lib\Legacy_FIXME\lte($xy, $oy));C(HH\Lib\Legacy_FIXME\gt($xy, $oy));C(HH\Lib\Legacy_FIXME\gte($xy, $oy));C(HH\Lib\Legacy_FIXME\eq($xy, $oy));C($xy===$oy);
  begin_row('fa', 'WRAPO');
    C($xy<$fy);C($xy<=$fy);C($xy>$fy);C($xy>=$fy);C($xy==$fy);C($xy===$fy);

  begin_row('va', 'WRAPD');
    C($xz<$vz);C($xz<=$vz);C($xz>$vz);C($xz>=$vz);C($xz==$vz);C($xz===$vz);
  begin_row('oa', 'WRAPD');
    C(HH\Lib\Legacy_FIXME\lt($xz, $oz));C(HH\Lib\Legacy_FIXME\lte($xz, $oz));C(HH\Lib\Legacy_FIXME\gt($xz, $oz));C(HH\Lib\Legacy_FIXME\gte($xz, $oz));C(HH\Lib\Legacy_FIXME\eq($xz, $oz));C($xz===$oz);
  begin_row('fa', 'WRAPD');
    C($xz<$fz);C($xz<=$fz);C($xz>$fz);C($xz>=$fz);C($xz==$fz);C($xz===$fz);
  print_footer();

  print_header('[dynamic] VAR ? $cm');
  begin_row('va');
    C($va<$cm);C($va<=$cm);C($va>$cm);C($va>=$cm);C($va==$cm);C($va===$cm);
  begin_row('oa');
    C(HH\Lib\Legacy_FIXME\lt($oa, $cm));C(HH\Lib\Legacy_FIXME\lte($oa, $cm));C(HH\Lib\Legacy_FIXME\gt($oa, $cm));C(HH\Lib\Legacy_FIXME\gte($oa, $cm));C(HH\Lib\Legacy_FIXME\eq($oa, $cm));C($oa===$cm);
  begin_row('fa');
    C($fa<$cm);C($fa<=$cm);C($fa>$cm);C($fa>=$cm);C($fa==$cm);C($fa===$cm);

  begin_row('va', 'WRAPA');
    C($vx<$xx);C($vx<=$xx);C($vx>$xx);C($vx>=$xx);C($vx==$xx);C($vx===$xx);
  begin_row('oa', 'WRAPA');
    C(HH\Lib\Legacy_FIXME\lt($ox, $xx));C(HH\Lib\Legacy_FIXME\lte($ox, $xx));C(HH\Lib\Legacy_FIXME\gt($ox, $xx));C(HH\Lib\Legacy_FIXME\gte($ox, $xx));C(HH\Lib\Legacy_FIXME\eq($ox, $xx));C($ox===$xx);
  begin_row('fa', 'WRAPA');
    C($fx<$xx);C($fx<=$xx);C($fx>$xx);C($fx>=$xx);C($fx==$xx);C($fx===$xx);

  begin_row('va', 'WRAPO');
    C($vy<$xy);C($vy<=$xy);C($vy>$xy);C($vy>=$xy);C($vy==$xy);C($vy===$xy);
  begin_row('oa', 'WRAPO');
    C(HH\Lib\Legacy_FIXME\lt($oy, $xy));C(HH\Lib\Legacy_FIXME\lte($oy, $xy));C(HH\Lib\Legacy_FIXME\gt($oy, $xy));C(HH\Lib\Legacy_FIXME\gte($oy, $xy));C(HH\Lib\Legacy_FIXME\eq($oy, $xy));C($oy===$xy);
  begin_row('fa', 'WRAPO');
    C($fy<$xy);C($fy<=$xy);C($fy>$xy);C($fy>=$xy);C($fy==$xy);C($fy===$xy);

  begin_row('va', 'WRAPD');
    C($vz<$xz);C($vz<=$xz);C($vz>$xz);C($vz>=$xz);C($vz==$xz);C($vz===$xz);
  begin_row('oa', 'WRAPD');
    C(HH\Lib\Legacy_FIXME\lt($oz, $xz));C(HH\Lib\Legacy_FIXME\lte($oz, $xz));C(HH\Lib\Legacy_FIXME\gt($oz, $xz));C(HH\Lib\Legacy_FIXME\gte($oz, $xz));C(HH\Lib\Legacy_FIXME\eq($oz, $xz));C($oz===$xz);
  begin_row('fa', 'WRAPD');
    C($fz<$xz);C($fz<=$xz);C($fz>$xz);C($fz>=$xz);C($fz==$xz);C($fz===$xz);
  print_footer();
}

<<__EntryPoint>>
function main() {
  static_compare();
  dynamic_compare();
}
