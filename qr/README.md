# PHP QR Generate Lib


include "qrlib.php";

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
QRcode::png( STRING ,false,'H', 4, 2);