<!DOCTYPE html>

<html>
	<title>Ejercicio XML</title>

<body>
	<?php
$url = "https://catalegdades.caib.cat/resource/rjfm-vxun.xml";
if (!$xml = file_get_contents($url)) {
	echo "No se ha podido cargar la url";
} else {
	//echo "Se ha podido cargar la url <br>";
	$xml = simplexml_load_string($xml);
	//var_dump($xml);
}

$fulldata=$xml->rows;
//var_dump($fulldata);
$i=0;
$rellenar = array();
foreach ($fulldata->row as $data) {
	$municipio = (string) $data->municipi;
	$adressa = $data->adre_a_de_l_establiment;
	$postalcode = intval(preg_replace('/[^0-9]+/', '', $adressa), 10);
	$nombre = $data->denominaci_comercial;
	if (isset($rellenar[$municipio])) {
		$rellenar[$municipio][] = array("nombre_comercial" => $nombre, "codigo_postal" => $postalcode);
	} else {
		$rellenar[$municipio] = array(array("nombre_comercial" => $nombre, "codigo_postal" => $postalcode));
	}
}
//var_dump($rellenar);
echo "<br>";

$municipio=isset($_GET["municipio"]) ? $_GET["municipio"] : "";
$postalcode=isset($_GET["codigo_postal"]) ? $_GET["codigo_postal"] : "";
$nombre=isset($_GET["nombre"]) ? $_GET["nombre"] : "";

ksort($rellenar)
?>

<form action="rentacar.php" method="get">
<label for="postalcodes">Elige el código postal:</label>
<select id="postalcodes" name="postalcode">
<?php 
	foreach ($rellenar as $municipio => $establecimientos) {
		foreach ($establecimientos as $establecimiento) {
			echo "<option value=\"" . $establecimiento["codigo_postal"] . "\">" . $establecimiento["codigo_postal"] . "</option>";
		}
	}
?>
</select>
<br>
<fieldset>
	<legend>Selecciona un municipio:</legend>
	<?php
		foreach ($rellenar as $municipio => $establecimientos) {
			echo "<input type=\"radio\" id=\"" . $municipio . "\" name=\"municipio\" value=\"" . $municipio . "\">";
			echo "<label for=\"" . $municipio . "\">" . $municipio . "</label><br>";
	}
	?>
</fieldset>
<br>
	Nombre de la empresa:<input type="text" name="nombre" value="<?php echo $nombre; ?>">
<?php
if(isset($_GET["municipio"]) || isset($_GET["postalcode"])) {
	$municipioSeleccionado = $_GET["municipio"];
	$postalcodeSeleccionado = $_GET["postalcode"];
	echo "<h2>Establecimientos en " . $municipioSeleccionado . " con código postal " . $postalcodeSeleccionado . "</h2>";
	echo "<table border='1'>";
	echo "<tr><th>Nombre Comercial</th></tr>";
	foreach ($rellenar[$municipioSeleccionado] as $establecimiento) {
	    if ($establecimiento["codigo_postal"] == $postalcodeSeleccionado) {
		echo "<tr><td>" . $establecimiento["nombre_comercial"] . "</td></tr>";
	    }
	}
	echo "</table>";
}
	?>

</body>
</html>
