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
			})) ?> mot(s) finis(sent) par la lettre « q »  </li>
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

		<li>Quel est le classement du film « Gravity » ? <br />
			<?php foreach ($top as $key => $value) {
				if($value["im:name"]['label'] == "Gravity")
					echo $key;
			} ?>
		</li>

		<li>Quel est le réalisateur du film « The LEGO Movie » ? <br />
			<?php foreach ($top as $key => $value) {
				if($value["im:name"]['label'] == "The LEGO Movie")
					echo $value["im:artist"]['label'];
			} ?>
		</li>

		<li>Combien de films sont sortis avant 2000 ? <br />
			<?php
			echo count(array_filter($top, function($film){
				return(explode('-', $film["im:releaseDate"]['label'])[0] < 2000);
			}))
			?>
		</li>

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

		<li>Quelle est la catégorie de films la plus représentée ? <br />
			<?php 
			$categorycount = [];
			foreach($top as $film){
				$categorycount[$film['category']['attributes']['label']] += 1;
			}
			echo array_search(max($categorycount), $categorycount);
			?>
		</li>

		<li>Quel est le réalisateur le plus présent dans le top100 ? <br />
			<?php 
			$artistcount = [];
			foreach($top as $film){
				$artistcount[$film['im:artist']['label']] += 1;
			}
			echo array_search(max($artistcount), $artistcount);
			?>
		</li>

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
	<?php
	// array(12) {
	// 	["im:name"]=> array(1) { ["label"]=> string(11) "The Martian" }
	// 	["im:image"]=> array(3) { [0]=> array(2) { ["label"]=> string(121) "http://is2.mzstatic.com/image/thumb/Video49/v4/27/97/1b/27971b4f-1d05-812b-a09f-0d51b9c7b264/pr_source.lsr/60x60bb-85.jpg" ["attributes"]=> array(1) { ["height"]=> string(2) "60" } } [1]=> array(2) { ["label"]=> string(121) "http://is2.mzstatic.com/image/thumb/Video49/v4/27/97/1b/27971b4f-1d05-812b-a09f-0d51b9c7b264/pr_source.lsr/60x60bb-85.jpg" ["attributes"]=> array(1) { ["height"]=> string(2) "60" } } [2]=> array(2) { ["label"]=> string(123) "http://is4.mzstatic.com/image/thumb/Video49/v4/27/97/1b/27971b4f-1d05-812b-a09f-0d51b9c7b264/pr_source.lsr/170x170bb-85.jpg" ["attributes"]=> array(1) { ["height"]=> string(3) "170" } } }
	// 	["summary"]=> array(1) {
	// 		["label"]=> string(378) "From legendary director Ridley Scott (Alien, Prometheus) comes a gripping tale of human strength and the will to survive. During a mission to Mars, American astronaut Mark Watney (Matt Damon) is presumed dead and left behind. But Watney is still alive. Against all odds, he must find a way to contact Earth in the hope that scientists can devise a rescue plan to bring him home." }
	// 		["im:price"]=> array(2) {
	// 			["label"]=> string(6) "$14.99" ["attributes"]=> array(2) { ["amount"]=> string(8) "14.99000" ["currency"]=> string(3) "USD"
	// 		}
	// 	}
	// 	["im:contentType"]=> array(1) {
	// 		["attributes"]=> array(2) { ["term"]=> string(5) "Movie" ["label"]=> string(5) "Movie" } } ["rights"]=> array(1) { ["label"]=> string(68) "© 2015 Twentieth Century Fox Film Corporation. All rights reserved." } ["title"]=> array(1) { ["label"]=> string(26) "The Martian - Ridley Scott" } ["link"]=> array(2) { [0]=> array(1) { ["attributes"]=> array(3) { ["rel"]=> string(9) "alternate" ["type"]=> string(9) "text/html" ["href"]=> string(63) "https://itunes.apple.com/us/movie/the-martian/id1039586890?uo=2" } } [1]=> array(2) { ["im:duration"]=> array(1) { ["label"]=> string(8) "173600.0" } ["attributes"]=> array(5) { ["title"]=> string(7) "Preview" ["rel"]=> string(9) "enclosure" ["type"]=> string(11) "video/x-m4v" ["href"]=> string(149) "http://a1631.v.phobos.apple.com/us/r1000/160/Video2/v4/61/c9/da/61c9da86-9cf4-47f8-1a20-e7d0ef2e8e44/mzvf_4438736739303881681.640x354.h264lc.D2.p.m4v" ["im:assetType"]=> string(7) "preview" } } } ["id"]=> array(2) { ["label"]=> string(63) "https://itunes.apple.com/us/movie/the-martian/id1039586890?uo=2" ["attributes"]=> array(1) { ["im:id"]=> string(10) "1039586890" } } ["im:artist"]=> array(1) { ["label"]=> string(12) "Ridley Scott" } ["category"]=> array(1) { ["attributes"]=> array(4) { ["im:id"]=> string(4) "4413" ["term"]=> string(16) "Sci-Fi & Fantasy" ["scheme"]=> string(67) "https://itunes.apple.com/us/genre/movies-sci-fi-fantasy/id4413?uo=2" ["label"]=> string(16) "Sci-Fi & Fantasy" } } ["im:releaseDate"]=> array(2) { ["label"]=> string(25) "2015-10-02T00:00:00-07:00" ["attributes"]=> array(1) { ["label"]=> string(15) "October 2, 2015" }
	// 	}
	// }
	?>
</body>
</html>