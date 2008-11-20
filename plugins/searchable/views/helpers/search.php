<?php
class SearchHelper extends AppHelper {

	var $name = 'Search';

	function highlight($terms, $string, $crop = 40, $delimiter = '...', $max_chars = 400){
		if(!is_array($terms)){
			$terms = array($terms);
		}
		$excerpt = '';
		if($crop) {
			$string = strip_tags($string);
			// get the term positions
			$positions = array();
			foreach($terms as $term){
				$offset = 0;
				while(strlen($string) > $offset){
					$position = stripos($string, $term, $offset);
					if($position !== FALSE){
						$positions[] = $position;
						$offset = $position + strlen($term);
					} else {
						break;
					}

				}
			}
			sort($positions);
			unset($position);
			if(!empty($positions)) {
				if(($positions[0] - $crop) > 0){
					$part_start = $positions[0] - $crop;
				} else {
					$part_start = 0;
				}
				for($pos = 0; $pos <= (count($positions) - 2); $pos++){
					if(($positions[$pos] + (2 *$crop)) < $positions[$pos + 1]){
						$excerpt .= mb_substr($string, $part_start, $positions[$pos] - $part_start + $crop, 'UTF8'). $delimiter.' '.$delimiter;
						$part_start = $positions[$pos + 1] - $crop;
					}
				}
				if((end($positions) + (2 *$crop)) < count($string)){
					$excerpt .= mb_substr($string, $part_start, $positions[$pos] - $part_start + $crop, 'UTF8'). $delimiter;
				} else {
					$excerpt .= mb_substr($string, end($positions) - $crop, strlen($string), 'UTF8');
				}

			}
			if(!empty($excerpt) && mb_substr($string, 0, 4, 'UTF8') != mb_substr($excerpt, 0, 4, 'UTF8')){
				$excerpt = '...'.$excerpt;
			}
			$string = $excerpt;
		}
		if($max_chars && strlen($string) > $max_chars){
			 $string = mb_substr($string, 0, $max_chars, 'UTF8');
		}
		$i = 0;
		foreach($terms as $term){
			$string = str_ireplace($term, '<span class="search_term_'.$i.'">'.$term.'</span>', $string);
			$i++;
		}
		return $string;
	}
}?>