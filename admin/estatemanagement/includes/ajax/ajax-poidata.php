<?php

/**********************************************************************************************************************************
*
* Ajax POI data
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


	add_action( 'PF_AJAX_HANDLER_pfget_markers', 'pf_ajax_markers' );
	add_action( 'PF_AJAX_HANDLER_nopriv_pfget_markers', 'pf_ajax_markers' );
	
	
function pf_ajax_markers(){
	check_ajax_referer( 'pfget_markers', 'security' );
	
	header('Content-type: text/javascript');		
		
		if(isset($_POST['cl']) && $_POST['cl']!=''){
			$pflang = esc_attr($_POST['cl']);
			global $sitepress;
			$sitepress->switch_lang($pflang);
		}else{
			$pflang = '';
		}

		function pf_term_sub_check($myval){
			$term_sub_check = get_term_by( 'term_id', $myval, 'pointfinderltypes');
		
			if ($term_sub_check != false) {

				if ($term_sub_check->parent == 0) {
					$output = $myval;
				}else{
					$output = pf_term_sub_check($term_sub_check->parent);
				}
			}else{
				$output = $myval;
			}

			return $output;
		}

		function pf_term_sub_check_ex($myval){
			$term_sub_check = get_term_by( 'term_id', $myval, 'pointfinderltypes');
		
			if ($term_sub_check != false) {

				if ($term_sub_check->parent == 0) {
					return true;
				}else{
					return false;
				}
			}
		}

	/** 
	*Start: GET - Marker Point Vars
	**/
		function pf_get_markerimage($postid){
			
			$pfitemicon = array();

			/* Check if item have a custom icon */

			$webbupointfinder_item_point_type = esc_attr(get_post_meta( $postid, "webbupointfinder_item_point_type", true ));
			$webbupointfinder_item_point_typenew = (empty($webbupointfinder_item_point_type))? 3:$webbupointfinder_item_point_type;
			
			
			$setup8_pointsettings_pointopacity = PFSAIssetControl('setup8_pointsettings_pointopacity','','0.7');

			switch ($webbupointfinder_item_point_typenew) {
				case 1:
					$pf_custom_point_images = redux_post_meta("pointfinderthemefmb_options", $postid, "webbupointfinder_item_custom_marker");

					/** 
					*Start: Custom icon check result = Image Icon
					**/
						$pfitemicon['is_image'] = 1;
						$pfitemicon['is_cat'] = 0;

						$pf_custom_point_image_height = (!empty($pf_custom_point_images['height']))? $pf_custom_point_images['height'] : 0;
						$pf_custom_point_image_width = (!empty($pf_custom_point_images['width']))? $pf_custom_point_images['width'] : 0;

						$setup8_pointsettings_retinapoints = PFSAIssetControl('setup8_pointsettings_retinapoints','','1');
						

						if ($setup8_pointsettings_retinapoints == 1) {
							$retina_number = 2;
						}else{
							$retina_number = 1;
						}

						$width_calculated = $pf_custom_point_image_width/$retina_number;
						$height_calculated = $pf_custom_point_image_height/$retina_number;

						$pfitemicon['content']= '<div class=\'pf-map-pin-x\' style=\'background-image:url('.$pf_custom_point_images['url'].'); background-size:'.$width_calculated.'px '.$height_calculated.'px; width:'.$width_calculated.'px; height:'.$height_calculated.'px;opacity:'.$setup8_pointsettings_pointopacity.'\' ></div>';
					/** 
					*End: Custom icon check result = Image Icon
					**/
				break;

			case 2:

				/** 
				*Start: Custom icon check result = Css Icon
				**/
					$cssmarker_icontype = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_icontype', true ));
					$cssmarker_icontype = (empty($cssmarker_icontype)) ? 1 : $cssmarker_icontype ;
					$cssmarker_iconsize = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconsize', true ));
					$cssmarker_iconsize = (empty($cssmarker_iconsize)) ? 'middle' : $cssmarker_iconsize ;
					$cssmarker_iconname = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconname', true ));

					$cssmarker_bgcolor = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_bgcolor', true ));
					$cssmarker_bgcolor = (empty($cssmarker_bgcolor)) ? '#b00000' : $cssmarker_bgcolor ;
					$cssmarker_bgcolorinner = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_bgcolorinner', true ));
					$cssmarker_bgcolorinner = (empty($cssmarker_bgcolorinner)) ? '#ffffff' : $cssmarker_bgcolorinner ;
					$cssmarker_iconcolor = esc_attr(get_post_meta( $postid, 'webbupointfinder_item_cssmarker_iconcolor', true ));
					$cssmarker_iconcolor = (empty($cssmarker_iconcolor)) ? '#b00000' : $cssmarker_iconcolor ;
					
					$arrow_text = ($cssmarker_icontype == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$cssmarker_bgcolor.' transparent transparent transparent;\'></div>': '';

					$pfitemicon['is_image'] = 1;
					$pfitemicon['is_cat'] = 0;

					$pfitemicon['content'] = '';
					
					
					$pfitemicon['content'] .= '<div ';
					$pfitemicon['content'] .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$cssmarker_icontype.' pf-map-pin-'.$cssmarker_icontype.'-'.$cssmarker_iconsize.' pfcustom-mapicon-'.$postid.'\'';
					$pfitemicon['content'] .= ' style=\'background-color:'.$cssmarker_bgcolor.';opacity:'.$setup8_pointsettings_pointopacity.';\' >';
					$pfitemicon['content'] .= '<i class=\''.$cssmarker_iconname.'\' style=\'color:'.$cssmarker_iconcolor.'\' ></i></div>'.$arrow_text;
					$pfitemicon['content'] .= '<style>.pfcustom-mapicon-'.$postid.':after{background-color:'.$cssmarker_bgcolorinner.'!important}</style>';

				/** 
				*End: Custom icon check result = Css Icon
				**/	
				break;

			default:
				/** 
				*Start: Check category icon 
				**/
					$pfitemicon['is_image'] = 0;
					$pfitemicon['is_cat'] = 1;

					$pf_item_terms = get_the_terms( $postid, 'pointfinderltypes');
					
					/* If marker term is available and array not empty */
					if(count($pf_item_terms) > 0){

						if ( $pf_item_terms && ! is_wp_error( $pf_item_terms ) ) {
							foreach ( $pf_item_terms as $pf_item_term ) {
								
								if ($pf_item_term->parent != 0) {
									$pf_item_term_subcheck = pf_term_sub_check_ex($pf_item_term->parent);
									if ($pf_item_term_subcheck) {
										$pf_item_term_id = $pf_item_term->term_id;
									}else{
										$pf_item_term_id = pf_term_sub_check($pf_item_term->term_id);
									}
									if (!empty($pf_item_term_id)) {
										break;
									}
								}else{
									$pf_item_term_id = $pf_item_term->term_id;
								}
								
								
							}

						} 

						if(function_exists('icl_object_id')) { /* If wpml enabled */
							$pf_item_term_id = icl_object_id($pf_item_term_id,'pointfinderltypes',true,PF_default_language());
						}

						if (!empty($pf_item_term_id)) {
							$pfitemicon['cat'] = 'pfcat'.$pf_item_term_id;
						}else{
							$pfitemicon['cat'] = 'pfcatdefault';
						}
					
						
					}
					
				/** 
				*End: Check category icon 
				**/
				break;
			}
			
			return $pfitemicon;
		}
	/** 
	*End: GET - Marker Point Vars
	**/
		
	


	/** 
	*Start: GET - Marker Category CSS Name
	**/
		function pointfinder_get_category_points($pf_get_term_detail_idm){
			$pf_get_term_detail_id = $pf_get_term_detail_idxx = $pf_get_term_detail_idm;
			$output_data = '';
			if(function_exists('icl_object_id')) { /* If wpml enabled */
				$pf_get_term_detail_id = icl_object_id($pf_get_term_detail_id,'pointfinderltypes',true,PF_default_language());
				//$pf_get_term_detail_idxx = icl_object_id($pf_get_term_detail_id,'pointfinderltypes',true,PF_current_language());
			}

			/*
				Loop Begin from PF Custom Points
			*/
				$setup8_pointsettings_retinapoints = PFSAIssetControl('setup8_pointsettings_retinapoints','','1');
				if ($setup8_pointsettings_retinapoints == 1) {
					$retina_number = 2;
				}else{
					$retina_number = 1;
				}

				$icon_type = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_type','','0');

				$icon_bg_image = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_bgimage','','0');

				$icon_layout_type = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_icontype','','1');
				$icon_name = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_iconname','','');
				$icon_size = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_iconsize','','middle');
				$icon_bg_color = PFPFIssetControl('pscp_'.$pf_get_term_detail_id.'_bgcolor','','#b00000');

				$setup8_pointsettings_pointopacity = PFSAIssetControl('setup8_pointsettings_pointopacity','','0.7');
				
				$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

				if ($icon_type == 0 && empty($icon_bg_image)) {

					$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
					$output_data .= ' "<div ';
					$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id.'-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
					$output_data .= ' >';
					$output_data .= '<i class=\''.$icon_name.'\' ></i></div>";'.PHP_EOL;
				
				}elseif ($icon_type != 0 && !empty($icon_bg_image)){

					$height_calculated = $icon_bg_image['height']/$retina_number;
					$width_calculated = $icon_bg_image['width']/$retina_number;

					$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
					$output_data .= ' "<div ';
					$output_data .= 'class=\'pf-map-pin-x\' ';
					$output_data .= 'style=\'background-image:url('.$icon_bg_image['url'].');opacity:'.$setup8_pointsettings_pointopacity.'; background-size:'.$width_calculated.'px '.$height_calculated.'px; width:'.$width_calculated.'px; height:'.$height_calculated.'px;\'';
					$output_data .= ' >';
					$output_data .= '</div>";'.PHP_EOL;
				
				}else{

					$output_data .= 'var pfcat'.$pf_get_term_detail_id.' =';
					$output_data .= ' "<div ';
					$output_data .= 'class=\'pfcat'.$pf_get_term_detail_id.'-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
					$output_data .= ' >';
					$output_data .= '<i class=\''.$icon_name.'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;

				}
				return $output_data;
		}
	/** 
	*End: GET - Marker Category CSS Name
	**/


	/** 
	*Start: GET - Cats Point Vars
	**/
		function pf_get_default_cat_images($pflang = ''){
			
			$wpflistdata = '';

			/**
			*Start: Default Point Variables
			**/
				$icon_layout_type = PFPFIssetControl('pscp_pfdefaultcat_icontype','','1');
				$icon_name = PFPFIssetControl('pscp_pfdefaultcat_iconname','','');
				$icon_size = PFPFIssetControl('pscp_pfdefaultcat_iconsize','','middle');
				$icon_bg_color = PFPFIssetControl('pscp_pfdefaultcat_bgcolor','','#b00000');

				$arrow_text = ($icon_layout_type == 2)? '<div class=\'pf-pinarrow\' style=\'border-color: '.$icon_bg_color.' transparent transparent transparent;\'></div>': '';

				$wpflistdata .= 'var pfcatdefault =';
				$wpflistdata .= ' "<div ';
				$wpflistdata .= 'class=\'pfcatdefault-mapicon pf-map-pin-'.$icon_layout_type.' pf-map-pin-'.$icon_layout_type.'-'.$icon_size.'\'';
				$wpflistdata .= ' >';
				$wpflistdata .= '<i class=\''.$icon_name.'\' ></i></div>'.$arrow_text.'";'.PHP_EOL;
			/**
			*End: Default Point Variables
			**/



			/**
			*Start: Cat Point Variables
			**/
				
				$pf_get_term_details = get_terms('pointfinderltypes',array('hide_empty'=>false)); 

				if (!empty($pflang) && function_exists('icl_object_id')) {/* If wpml enabled */
					global $sitepress;
					$sitepress->switch_lang($pflang);
				}

				if(count($pf_get_term_details) > 0){
					
					foreach ( $pf_get_term_details as $pf_get_term_detail ) {
						if ($pf_get_term_detail->parent == 0) {
							
							$wpflistdata .= pointfinder_get_category_points($pf_get_term_detail->term_id);

							$pf_get_term_details_sub = get_terms('pointfinderltypes',array('hide_empty'=>false,'parent'=>$pf_get_term_detail->term_id)); 

							foreach ($pf_get_term_details_sub as $pf_get_term_detail_sub) {
								$wpflistdata .= pointfinder_get_category_points($pf_get_term_detail_sub->term_id);
							}

						}
						
					}
					/*
						Loop End from PF Custom Points
					*/

		
				}

			/**
			*End: Cat Point Variables
			**/

			return $wpflistdata;
		}
	/** 
	*End: GET - Marker Cats Vars
	**/

		
		
		/* Write data - Check this !! */
		$wpflistdata = '';
		
		/* Define taxonomy point icons */
		$wpflistdata .= pf_get_default_cat_images();
		/* Define taxonomy point icons finished */

		/* Get admin values */
		$setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
		
		/*Get point limits*/
		if(isset($_POST['spl']) && $_POST['spl']!=''){
			$setup8_pointsettings_limit = $_POST['spl'];
			$setup8_pointsettings_orderby = $_POST['splob'];
			$setup8_pointsettings_order = $_POST['splo'];
		}else{
			$setup8_pointsettings_limit = -1;
		}
		$setup14_multiplepointsettings_check = PFSAIssetControl('setup14_multiplepointsettings_check','','1');
		
		/*Search form check*/
		if(isset($_POST['act']) && $_POST['act']!=''){
			$pfaction = esc_attr($_POST['act']);
		}else{
			$pfaction = '';
		}

		/*Current Language*/
		if(isset($_POST['cl']) && $_POST['cl']!=''){
			$pflang = esc_attr($_POST['cl']);
			global $sitepress;
			$sitepress->switch_lang($pflang);
		}else{
			$pflang = '';
		}
		
		
		$args = array( 'post_type' => $setup3_pointposttype_pt1, 'posts_per_page' => $setup8_pointsettings_limit, 'post_status' => 'publish');
		
		
		if($setup8_pointsettings_limit > 0){
			
			if($setup8_pointsettings_orderby != ''){$args['orderby']=$setup8_pointsettings_orderby;};
			if($setup8_pointsettings_order != ''){$args['order']=$setup8_pointsettings_order;};
			
		}
		
		function isJson($string) {
		 json_decode($string);
		 return (json_last_error() == JSON_ERROR_NONE);
		}
					
		if($pfaction == 'search'){
			if(isset($_POST['dt']) && $_POST['dt']!=''){
				$pfgetdata = $_POST['dt'];
				if (!is_array($pfgetdata)) {
					$pfgetdata = maybe_unserialize(base64_decode($pfgetdata,true));
					if (is_array($pfgetdata)) {
						foreach ($pfgetdata as $key => $value) {
							$pfnewgetdata[] = array('name' => $key, 'value'=>$value);
						}
						$pfgetdata = $pfnewgetdata;
					}
				}
			}else{
				$pfgetdata = '';
			}
			
				if(is_array($pfgetdata)){
					
					$pfformvars = array();
					
						foreach($pfgetdata as $singledata){
							
							/*Get Values & clean*/
							if(esc_attr($singledata['value']) != ''){
								
								if(isset($pfformvars[esc_attr($singledata['name'])])){
									$pfformvars[esc_attr($singledata['name'])] = $pfformvars[esc_attr($singledata['name'])]. ',' .$singledata['value'];
								}else{
									$pfformvars[esc_attr($singledata['name'])] = $singledata['value'];
								}
		
							}
						
						}
						$pfgetdata = PFCleanArrayAttr('PFCleanFilters',$pfgetdata);
						foreach($pfformvars as $pfformvar => $pfvalue){
							
							$thiskeyftype = '';
							$thiskeyftype = PFFindKeysInSearchFieldA_ld($pfformvar);
							
							/*Get target field & condition*/
							$target = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target','','');
							$target_condition = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target_according','','');

							switch($thiskeyftype){
								case '1':/*select*/
									/*is_Multiple*/
									$multiple = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_multiple','','0');
									
									/*
									Find Select box type
									Check element: is it a taxonomy?
									*/
									$rvalues_check = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_check','','0');
									
									if($rvalues_check == 0){
										
										$pfvalue_arr = PFGetArrayValues_ld($pfvalue);
										
										$fieldtaxname = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_posttax','','');
										if(isset($args['tax_query']) == false || isset($args['tax_query']) == NULL){
											$args['tax_query'] = array();
										}
										if(count($args['tax_query']) > 0){
											$args['tax_query'][(count($args['tax_query'])-1)]=
											array(
													'taxonomy' => $fieldtaxname,
													'field' => 'id',
													'terms' => $pfvalue_arr,
													'operator' => 'IN'
											);
										}else{
											$args['tax_query']=
											array(
												'relation' => 'AND',
												array(
													'taxonomy' => $fieldtaxname,
													'field' => 'id',
													'terms' => $pfvalue_arr,
													'operator' => 'IN'
												)
											);
										}
						
									}else{
										$target_r = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_target_target','','');
										$target_condition_r = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_rvalues_target_according','','');
										
										
										if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
											$args['meta_query'] = array();
										}
										
										if(is_numeric($pfvalue)){
											$pfcomptype = 'NUMERIC';
										}else{
											$pfcomptype = 'CHAR';
										}
										
										if(count($args['meta_query']) > 0){
											$args['meta_query'][(count($args['meta_query'])-1)] = 
												array(
												'key' => 'webbupointfinder_item_'.$target_r,
												'value' => $pfvalue,
												'compare' => $target_condition_r,
												'type' => $pfcomptype
												
											);
										}else{
											$args['meta_query'] = array(
												'relation' => 'AND',
												array(
												'key' => 'webbupointfinder_item_'.$target_r,
												'value' => $pfvalue,
												'compare' => $target_condition_r,
												'type' => $pfcomptype
												),
											);
										}
									}
									
									break;
									
								case '2':/*slider*/
									/*Find Slider Type from slug*/
									$slidertype = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_type','','');
									$pfcomptype = 'NUMERIC';
									
									if($slidertype == 'range'){ 
									$pfvalue = trim($pfvalue,"\0");
										$pfvalue_exp = explode(',',$pfvalue);
										if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
											$args['meta_query'] = array();
										}
																		
										if(count($args['meta_query']) > 0){
											$args['meta_query'][(count($args['meta_query'])-1)] = 
												array(
												'key' => 'webbupointfinder_item_'.$target,
												'value' => $pfvalue_exp[0],
												'compare' => '>=',
												'type' => $pfcomptype
												);
											$args['meta_query'][(count($args['meta_query']))] = 
												array(
												'key' => 'webbupointfinder_item_'.$target,
												'value' => $pfvalue_exp[1],
												'compare' => '<=',
												'type' => $pfcomptype
												);
											
										}else{
												$args['meta_query'] = array(
													'relation' => 'AND',
													array(
													'key' => 'webbupointfinder_item_'.$target,
													'value' => $pfvalue_exp[0],
													'compare' => '>=',
													'type' => $pfcomptype
												),
												array(
													'key' => 'webbupointfinder_item_'.$target,
													'value' => $pfvalue_exp[1],
													'compare' => '<=',
													'type' => $pfcomptype
												)
											);

										}
									}else{

										if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
											$args['meta_query'] = array();
										}	
										if(count($args['meta_query']) > 0){
											$args['meta_query'][(count($args['meta_query'])-1)] = 
											array(
												'key' => 'webbupointfinder_item_'.$target,
												'value' => $pfvalue,
												'compare' => $target_condition,
												'type' => $pfcomptype
												
											);
										}else{
											$args['meta_query'] = array(
												'relation' => 'AND',
												array(
												'key' => 'webbupointfinder_item_'.$target,
												'value' => $pfvalue,
												'compare' => $target_condition,
												'type' => $pfcomptype
												),
											);
										}
									}
									
									
									break;
									
								case '4':/*text field*/
									$target = PFSFIssetControl('setupsearchfields_'.$pfformvar.'_target_target','','');
									
									switch ($target) {
										case 'title':
												$args['search_prod_title'] = $pfvalue;
												function title_filter( $where, &$wp_query )
												{
													global $wpdb;
													if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
														if($search_term != ''){
															$search_term = $wpdb->esc_like( $search_term );
															$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(  $search_term ) . '%\'';
														}
													}
													return $where;
												}

										  		add_filter( 'posts_where', 'title_filter', 10, 2 );

											break;

										case 'google':
												
											break;

										case 'address':

												
												$pfcomptype = 'CHAR';

												if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
													$args['meta_query'] = array();
												}
																				
												if(count($args['meta_query']) > 0){
													$args['meta_query'][(count($args['meta_query'])-1)] = 
														array(
														'key' => 'webbupointfinder_items_address',
														'value' => $pfvalue,
														'compare' => 'LIKE',
														'type' => $pfcomptype
														);
													
												}else{
														$args['meta_query'] = array(
															'relation' => 'AND',
															array(
															'key' => 'webbupointfinder_items_address',
															'value' => $pfvalue,
															'compare' => 'LIKE',
															'type' => $pfcomptype
														)
													);

												}
											
											break;
										
										default:

												$pfcomptype = 'CHAR';

												if(isset($args['meta_query']) == false || isset($args['meta_query']) == NULL){
													$args['meta_query'] = array();
												}
																				
												if(count($args['meta_query']) > 0){
													$args['meta_query'][(count($args['meta_query'])-1)] = 
														array(
														'key' => 'webbupointfinder_item_'.$target,
														'value' => $pfvalue,
														'compare' => 'LIKE',
														'type' => $pfcomptype
														);
													
												}else{
														$args['meta_query'] = array(
															'relation' => 'AND',
															array(
															'key' => 'webbupointfinder_item_'.$target,
															'value' => $pfvalue,
															'compare' => 'LIKE',
															'type' => $pfcomptype
														)
													);

												}
											break;
									}
									
									break;
							}
							
						}
					
				}
		}else{
			
			if(isset($_POST['singlepoint']) && !empty($_POST['singlepoint'])){
				$pfitem_singlepoint = esc_attr($_POST['singlepoint']);
				$args['p'] = $pfitem_singlepoint;
				$args['suppress_filters'] = true;
			}
			
		}


		if(isset($_POST['dtx']) && $_POST['dtx']!='' && isset($_POST['dt']) == false ){

			$pfgetdatax = $_POST['dtx'];
			$pfgetdatax = PFCleanArrayAttr('PFCleanFilters',$pfgetdatax);


			if (is_array($pfgetdatax)) {
				foreach ($pfgetdatax as $key => $value) {

					if(isset($value['value'])){
						if (!empty($value['value'])) {
						
							if(isset($args['tax_query']) == false || isset($args['tax_query']) == NULL){
								$args['tax_query'] = array();
							}
							if(count($args['tax_query']) > 0){
								$args['tax_query'][(count($args['tax_query'])-1)]=
								array(
										'taxonomy' => $value['name'],
										'field' => 'id',
										'terms' => pfstring2BasicArray($value['value']),
										'operator' => 'IN'
								);
							}else{
								$args['tax_query']=
								array(
									'relation' => 'AND',
									array(
										'taxonomy' => $value['name'],
										'field' => 'id',
										'terms' => pfstring2BasicArray($value['value']),
										'operator' => 'IN'
									)
								);
							}
						}
					}
				}
			}

		}

		$setup8_pointsettings_pointopacity = PFSAIssetControl('setup8_pointsettings_pointopacity','','0.7');

		$wpflistdata .= PHP_EOL.'var wpflistdata = [';
		$loop = new WP_Query( $args );
		
			if($loop->post_count > 0){
		
				while ( $loop->have_posts() ) : $loop->the_post();
				
				$coordinates = explode( ',', rwmb_meta('webbupointfinder_items_location') );
				$post_id = get_the_id();
				
				/* Print out term listing type ------------------------------------------------------------------*/
					
					$term_list = wp_get_post_terms($post_id, 'pointfinderltypes', array("fields" => "ids"));
					
					$term_list_output = '[';
					
					foreach ($term_list as $myterm) {
						$term_list_output .= "'lt".$myterm."',";
					}
					
					$term_list_output .= '\'pfmarker\']';
					
				/* Print out term listing type ------------------------------------------------------------------*/
				
				
				
				
				/* Print out icon image -------------------------------------------------------------------------*/
					
					$pfitemicon = pf_get_markerimage($post_id);
					
				/* Print out icon image -------------------------------------------------------------------------*/
				
					
				
				
				/* Print out icon visibility --------------------------------------------------------------------*/
				
					$pfitemvisibilityGet = redux_post_meta("pointfinderthemefmb_options", $post_id, "webbupointfinder_item_point_visibility");
					$pfitemvisibilityGet = (empty($pfitemvisibilityGet))? 1: $pfitemvisibilityGet;
					
					if($pfitemvisibilityGet == 0){$pfitemvisibility = 'false';}else{$pfitemvisibility = 'true';}
				
				/* Print out icon visibility --------------------------------------------------------------------*/
					
					
					/* Check if lat,lng empty */
					if(isset($_POST['ne']) && $_POST['ne']!=''){
						$ne = esc_attr($_POST['ne']);
					}else{
						$ne = 360;
					}
					
					if(isset($_POST['ne2']) && $_POST['ne2']!=''){
						$ne2 = esc_attr($_POST['ne2']);
					}else{
						$ne2 = 360;
					}
					
					if(isset($_POST['sw']) && $_POST['sw']!=''){
						$sw = esc_attr($_POST['sw']);
					}else{
						$sw = -360;
					}
					
					if(isset($_POST['sw2']) && $_POST['sw2']!=''){
						$sw2 = esc_attr($_POST['sw2']);
					}else{
						$sw2 = -360;
					}
					

					if($coordinates[0] > $sw && $coordinates[0] < $ne && $coordinates[1] > $sw2 && $coordinates[1] < $ne2 && $coordinates[0] != '' && $coordinates[1] != ''){
							
							
							$wpflistdata .=  '{latLng:['.$coordinates[0].','.$coordinates[1].'],';
								if ($pflang) {
									$wpflistdata .= 'data:{id:'.PFLangCategoryID_ld($post_id,$pflang).'},id:'.PFLangCategoryID_ld($post_id,$pflang).',';
								}else{
									$wpflistdata .= 'data:{id:'.$post_id.'},id:'.$post_id.',';
								}
								$wpflistdata .= 'options:{';
								if(PFControlEmptyArr($pfitemicon)){
									
									if ($pfitemicon['is_cat'] == 1) {
										$wpflistdata .= "content:".$pfitemicon['cat'].",flat: true,";
									}

									if ($pfitemicon['is_image'] == 1) {
										$wpflistdata .= "content:\"".$pfitemicon['content']."\",flat: true,";
									}
									
								}
								$wpflistdata .= 'visible: '.$pfitemvisibility.'';
									if($term_list_output != '[]'){
										$wpflistdata .=',tag:"pfmarker"';
									}else{
										$wpflistdata .=',tag:"pfmarker"';
									}
									
									$wpflistdata .='}},'.PHP_EOL;
							
						}
					
					
				endwhile;
			
			}
			
			
		$wpflistdata .= '];';
		
		echo $wpflistdata;
		
	die();
}

?>