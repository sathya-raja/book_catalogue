<?php
/**
 * @file
 * Contains \Drupal\books\Controller\BooksController.
 */
 
namespace Drupal\books\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Render\FormattableMarkup;

class BooksController extends ControllerBase {

	public function bookslists(){
		global $base_path,$base_url;
		$entity_type = 'node';
		$type='books';
		$header = array('Title' ,'Genre','Price','Author','Published Year','Rating','Action'); 
		$db = \Drupal::database();

		$query = $db->select('node_field_data','n')
		->fields('n',array('nid'));
		$query->condition('n.status','1' ,'=');
		$query->condition('n.type',$type ,'=');


		// The actual action of sorting the rows is here.
		$table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
		->orderByHeader($header);
		// Limit the rows to 20 for each page.
		$pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
		->limit(1);
		$result = $pager->execute();
		
		// Populate the rows.
		$rows = array();
		foreach($result as $rslt){
			$nd_load = node_load($rslt->nid);
			$nid    = $rslt->nid;
			$url    =  $base_url.'/node/'.$nid.'/edit';
			$title  = $nd_load->get('title')->value;  
			$price  = $nd_load->get('field_price')->value; 
			$genre  = $nd_load->get('field_genre')->value; 
			$author = $nd_load->get('field_author')->value; 
			$pblshd = $nd_load->get('field_published_year')->value; 
			$rating = $nd_load->get('field_rating')->value;	
			
			$rows[] = array('data' => array(
					'title' => $title, 
					'gener' => $genre,
					'price' => 'Rs '.$price,
					'author'=> $author,
					'published_year'=> $pblshd,
					'rating'=> $rating,
					'action'=> new FormattableMarkup('<a href=":link">@name</a>', [':link' => $url, '@name' => 'Edit']),
			));		
		}
		
		// The table description.
		$build1 = array(
				'#markup' => t('')
		);
		 
		// Generate the table.
		$build1['config_table'] = array(
				'#theme' => 'table',
				'#header' => $header,
				'#rows' => $rows,
		);
		 
		// Finally add the pager.
		$build1['pager'] = array(
				'#type' => 'pager'
		);
		return array(
			'#theme' => 'books_lists',
			'#title' => 'List Of Books',
			'#items' => $build1,
			
		);
	}
	
}
?>
