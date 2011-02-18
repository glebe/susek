<?

echo strip_tags("<a href='link'>im link</a> <b>im bold</b> im text");
echo "<br>";
echo strip_tags("<a href='link'>im link</a> <b>im bold</b> im text","<a>");
echo "<br>";
echo strip_tags("<a href='link'>im link</a> <b>im bold</b> im text","<a><b>");
echo "<br>";
?>