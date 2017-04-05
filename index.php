<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Crunching</title>
</head>
<body>

	<?php
	$string = file_get_contents("dictionnaire.txt", FILE_USE_INCLUDE_PATH);
	$dico = explode("\n", $string);
	?>
	<ul>Exercices dico :
		<li><?php echo count($dico); ?>  mot(s) dans le dico</li>
		<li><?php echo count(array_filter($dico, function($mot){
			return(strlen($mot) >= 15 );
		})) ?> mot(s) d'exactement 15 caractères</li>
		<li><?php echo count(array_filter($dico, function($mot){
			return(strpos($mot, "w") > -1 );
		})) ?> mot(s) contiennent la lettre « w » </li>
		<li><?php 
			echo count(array_filter($dico, function($mot){
				return(strpos($mot, "q") == strlen($mot));
			})) ?> mot(s) finis(sent) par la lettre « q » 
		</li>
	</ul>
	<?php
	$string = file_get_contents("films.json", FILE_USE_INCLUDE_PATH);
	$brut = json_decode($string, true);
	$top = $brut["feed"]["entry"]; # liste de films
	?>
	<ul>Exercices films :
		<li>Afficher le top10 des films sous cette forme :
			<ul>
				<?php
				for($i=0; $i < 10; $i++)
					echo "<li>" . ($i+1) . "\n" . $top[$i]["im:name"]['label'] . "</li>"
				?>
			</ul>
		</li>
		<hr>
		<li>Quel est le classement du film « Gravity » ? <br />
			<?php
			foreach ($top as $key => $value) {
				if($value["im:name"]['label'] == "Gravity")
					echo $key;
			}
			?>
		</li>
		<hr>
		<li>Quel est le réalisateur du film « The LEGO Movie » ? <br />
			<?php
			foreach ($top as $key => $value) {
				if($value["im:name"]['label'] == "The LEGO Movie")
					echo $value["im:artist"]['label'];
			}
			?>

		</li>
		<hr>
		<li>Quel est le réalisateur du film « The LAGO Movie » ?(version Exception) <br />
			<?php
			try{
				foreach ($top as $key => $value) {
					if($value["im:name"]['label'] == "The LAGO Movie")
						throw new Exception($value["im:artist"]['label']);
				}
				throw new Exception("pas de film de ce nom dans la liste");
				
			}catch(Exception $e){
				echo $e->getMessage();
			}
			?>
		</li>
		<hr>
		<li>Combien de films sont sortis avant 2000 ? <br />
			<?php
			echo count(array_filter($top, function($film){
				return(explode('-', $film["im:releaseDate"]['label'])[0] < 2000);
			}))
			?>
		</li>
		<hr>
		<li>Combien de films sont sortis avant 1946 ? (version Exception) <br />
			<?php
			try {
				$avantdate = count(array_filter($top, function($film){
					return(explode('-', $film["im:releaseDate"]['label'])[0] < 1946);
				}));
				if($avantdate > 0)throw new Exception($avantdate);
				throw new Exception($avantdate);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			?>
		</li>
		<hr>
		<li>Quel est le film le plus récent ? Le plus vieux ? <br />
			<?php 
			$topsort = $top;
			usort($topsort, function($a,$b){
				if ($a["im:releaseDate"]['label'] == $b["im:releaseDate"]['label']) {
					return 0;
				}
				return ($a["im:releaseDate"]['label'] < $b["im:releaseDate"]['label']) ? -1 : 1;
			});
			echo "le plus vieux film est \"" . $topsort[0]['im:name']['label'] . "\" et le plus récent est \"" . $topsort[count($topsort)-1]['im:name']['label'] . "\" <br />";
			?>
		</li>
		<hr>
		<li>Quelle est la catégorie de films la plus représentée ? <br />
			<?php 
			$categorycount = [];
			foreach($top as $film){
				$categorycount[$film['category']['attributes']['label']] += 1;
			}
			echo array_search(max($categorycount), $categorycount);
			?>
		</li>
		<hr>
		<li>Quel est le réalisateur le plus présent dans le top100 ? <br />
			<?php 
			$artistcount = [];
			foreach($top as $film){
				$artistcount[$film['im:artist']['label']] += 1;
			}
			echo array_search(max($artistcount), $artistcount);
			?>
		</li>
		<hr>
		<li>Combien cela coûterait-il d'acheter le top10 sur iTunes ? de le louer ? <br />
			<?php 
			$rentaltotal = 0;
			$pricetotal = 0;
			for ($i=1; $i<=10; $i++) {
				$rentaltotal += substr($top[$i]['im:rentalPrice']['label'], 1);
				$pricetotal += substr($top[$i]['im:price']['label'], 1);
			}
			echo "rental top 10 price = " . $rentaltotal . "$ <br />buy top 10 price = " . $pricetotal . "$";
			?>
		</li>
		<hr>
		<li>Quel est le mois ayant vu le plus de sorties au cinéma ? <br />
			<?php 
			$monthcount = [];
			foreach ($top as $key => $value) {
				$monthcount[explode(' ',$value['im:releaseDate']['attributes']['label'])[0]] += 1;
			}
			$monthcountarray = array_keys($monthcount, max($monthcount));

			if($monthcountarray > 1){
				echo '<ul>les mois ayant vu le plus de sorties sont :';
				foreach ($monthcountarray as $value) {
					echo "<li>$value</li>";
				};
				echo '</ul>';
			}else{
				echo '<ul>le mois ayant vu le plus de sorties est ' . $monthcountarray[0];
			}
			?>
		</li>
		<hr>
		<li>Quels sont les 10 meilleurs films à voir en ayant un budget limité ?
			<?php
			$tabPrice = [];
			$tabClass = [];
			$tabTitle = [];
			foreach ($top as $key => $value) {
				$tabPrice[] = substr($value['im:price']['label'], 1);
				$tabClass[] = $key;
				$tabTitle[] = $value['im:name']['label'];
			}
			array_multisort($tabPrice, SORT_ASC, $tabClass, SORT_NUMERIC, $tabTitle);
			for($i = 0; $i < 10; $i++) {
				echo '<br>n°: '.$tabClass[$i].' - '.$tabTitle[$i].': $'.$tabPrice[$i];
			}
			echo '<hr>';
			?>
		</li>
	</ul>
</body>
</html>