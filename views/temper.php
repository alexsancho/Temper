<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Temper (Template Parser) Demo</title>
</head>
<body>
    <fieldset>
        <legend>Unparsed HTML</legend>
        <pre><?=$unparsed;?></pre>
    </fieldset><br />
    <fieldset>
        <legend>Parsed HTML</legend>
        <pre><?=$parsed;?></pre>
    </fieldset><br />
    <fieldset>
        <legend>Evaluated Code</legend>
        <pre><?=$evaled;?></pre>
        <hr />
    </fieldset><br />
</body>
</html>