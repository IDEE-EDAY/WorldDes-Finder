<?php
/**********************************************************************************************************************************
*
* Custom Search Fields Retrieve Value Class
* This class prepared for help to create auto config file.
* Author: Webbu Design
*
***********************************************************************************************************************************/
if ( ! class_exists( 'PF_SF_Val' ) ){
	class PF_SF_Val{
		
		public $FieldOutput;
		public $PFHalf = 1;
		public $ScriptOutput;
		public $ScriptOutputDocReady;
		public $VSORules;
		public $VSOMessages;


		public function __construct(){}
		
		function PriceFieldCheck($slug){
			if(PFCFIssetControl('setupcustomfields_'.$slug.'_currency_check','','0') == 1){
				return array(
					'CFPrefix' => PFCFIssetControl('setupcustomfields_'.$slug.'_currency_prefix','',''),
					'CFSuffix' => PFCFIssetControl('setupcustomfields_'.$slug.'_currency_suffix','',''),
					'CFDecima' => PFCFIssetControl('setupcustomfields_'.$slug.'_currency_decima','','0'),
					'CFDecimp' => PFCFIssetControl('setupcustomfields_'.$slug.'_currency_decimp','','.'),
					'CFDecimt' => PFCFIssetControl('setupcustomfields_'.$slug.'_currency_decimt','',',')
				);
			}else{return 'none';	}
		}
		
		function SizeFieldCheck($slug){
			if(PFCFIssetControl('setupcustomfields_'.$slug.'_size_check','','0') == 1){
				return array(
					'CFPrefix' => PFCFIssetControl('setupcustomfields_'.$slug.'_size_prefix','',''),
					'CFSuffix' => PFCFIssetControl('setupcustomfields_'.$slug.'_size_suffix','',''),
					'CFDecima' => 0,
					'CFDecimp' => PFCFIssetControl('setupcustomfields_'.$slug.'_size_decimp','','.'),
					'CFDecimt' => '.'
				);
			}else{return 'none';	}
		}
		
		function CheckItemsParent($slug){
			$RelationFieldName = 'setupcustomfields_'.$slug.'_parent';

			$ParentItem = PFCFIssetControl($RelationFieldName,'','');
			
	
			if(!empty($ParentItem)){
				
				if(function_exists('icl_object_id')) {
					if (is_array($ParentItem)) {
						foreach ($ParentItem as $key => $value) {
							$ParentItem[$key] = icl_object_id($value,'pointfinderltypes',true,PF_current_language());
						}
					}else{
						$ParentItem = icl_object_id($ParentItem,'pointfinderltypes',true,PF_current_language());
					}
					return $ParentItem;
					
				} else {
					return $ParentItem;
				}
			}else{
				return 'none';
			}
		}
		
		
		function GetValue($title,$slug,$ftype,$widget=0,$pfgetdata=array(),$hormode=0){
					
					global $pfsearchfields_options;
					
					
					switch($ftype){
						case '1':
						/* Select Box */
							$showonlywidget = PFSFIssetControl('setupsearchfields_'.$slug.'_showonlywidget','','0');
							$showonlywidget_check = 'show';

							if ($showonlywidget == 0 && $widget == 0) {
								$showonlywidget_check = 'show';
							}elseif ($showonlywidget == 1 && $widget == 0) {
								$showonlywidget_check = 'hide';
							}else{
								$showonlywidget_check = 'show';
							}

							if ($showonlywidget_check == 'show') {
								$target = PFSFIssetControl('setupsearchfields_'.$slug.'_rvalues_target_target','','');
								$itemparent = $this->CheckItemsParent($target);
								/*Check element: is it a taxonomy?*/
								$rvalues_check = PFSFIssetControl('setupsearchfields_'.$slug.'_rvalues_check','','0');
								if($itemparent == 'none'){
									$validation_check = PFSFIssetControl('setupsearchfields_'.$slug.'_validation_required','','0');
									if($validation_check == 1){
										$validation_message = PFSFIssetControl('setupsearchfields_'.$slug.'_message','','');
										if($this->VSOMessages != ''){
											$this->VSOMessages .= ','.$slug.':"'.$validation_message.'"';
										}else{
											$this->VSOMessages = $slug.':"'.$validation_message.'"';
										}
										
										if($this->VSORules != ''){
											$this->VSORules .= ','.$slug.':"required"';
										}else{
											$this->VSORules = $slug.':"required"';
										}
									}
									
									$select2_style = PFSFIssetControl('setupsearchfields_'.$slug.'_select2','','0');
									if($select2_style == 0){
										$select2sh = ', minimumResultsForSearch: -1';
									}else{ $select2sh = '';}
									
									$placeholder = PFSFIssetControl('setupsearchfields_'.$slug.'_placeholder','','');
									if($placeholder == ''){ $placeholder = esc_html__('Please select','pointfindert2d');};
									$nomatch = (isset($pfsearchfields_options['setupsearchfields_'.$slug.'_nomatch']))?$pfsearchfields_options['setupsearchfields_'.$slug.'_nomatch']:'';
									if($nomatch == ''){ $nomatch = '';};
									
									$column_type = PFSFIssetControl('setupsearchfields_'.$slug.'_column','','0');
									$multiple = PFSFIssetControl('setupsearchfields_'.$slug.'_multiple','','0');
									if($multiple == 1){ $multiplevar = 'multiple';}else{$multiplevar = '';};
									
									
									
									if($column_type == 1){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '<div class="col6 last"><div id="'.$slug.'_main">';
										}else{
											if ($hormode == 1 && $widget == 0) {
												$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
											}
											$this->FieldOutput .= '<div class="row"><div class="col6 first"><div id="'.$slug.'_main">';
										}
										$this->PFHalf++;
									}else{
										if ($hormode == 1 && $widget == 0) {
											$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
										}
										$this->FieldOutput .= '<div id="'.$slug.'_main">';
									};


									/*/Begin to create Select Box*/
									$this->ScriptOutput .= '$("#'.$slug.'").select2({placeholder: "'.esc_js($placeholder).'", formatNoMatches:"'.esc_js($nomatch).'",allowClear: true'.$select2sh.'});';
									

									$fieldtext = PFSFIssetControl('setupsearchfields_'.$slug.'_fieldtext','','');
									$this->FieldOutput .= '<div class="pftitlefield">'.$fieldtext.'</div>';
									$this->FieldOutput .= '<label for="'.$slug.'" class="lbl-ui select">';
									$this->FieldOutput .= '<select '.$multiplevar.' id="'.$slug.'" name="'.$slug.'" >';
									

									if($rvalues_check == 0){
									/* If this is a taxonomy */
										$fieldtaxname = PFSFIssetControl('setupsearchfields_'.$slug.'_posttax','','');
										$fieldtaxgroup = PFSFIssetControl('setupsearchfields_'.$slug.'_posttax_group','','0');
										$fieldtaxgroup_ex = PFSFIssetControl('setupsearchfields_'.$slug.'_posttax_group_ex','','1');
										$fieldtaxmove = PFSFIssetControl('setupsearchfields_'.$slug.'_posttax_move','','0');
										$mylat = PFSAIssetControl('setup5_mapsettings_lat','','');
										$mylng = PFSAIssetControl('setup5_mapsettings_lng','','');
										
										if($fieldtaxmove == 1 && $multiple != 1 && $widget == 0){ 
											/* If this is location and select move enabled. */
											/* GET - points per location */
											$this->ScriptOutput .= '$( "#'.$slug.'" ).change(function(){
													if($( "#'.$slug.'" ).val() != ""){
															if($.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()] != null){
																var mylatlng = new google.maps.LatLng($.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()][0],$.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()][1]);
																if($.pfgmap3static.pfmapobj != null && mylatlng != null){
																	$.pfmap_recenter($.pfgmap3static.pfmapobj,mylatlng,0,0);
																}
															}else{
																$.ajax({
																	beforeSend: function(){$(".pfmaploading").fadeIn("fast");},
																	type: "POST",
																	dataType: "script",
																	url: theme_map_functionspf.ajaxurl,
																	data: { 
																		"action": "pfget_taxpoint",
																		"id": $( "#'.$slug.'" ).val(),
																		"security": theme_map_functionspf.pfget_taxpoint,
																		"cl":$.pfgmap3static.currentlang
																	},
																	success:function(data){
																		if($.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()] != null){
																			var mylatlng = new google.maps.LatLng($.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()][0],$.pfgmap3static.movemaplatlng[$( "#'.$slug.'" ).val()][1]);
																		}else{
																			var mylatlng = new google.maps.LatLng('.$mylat.','.$mylng.');	
																		};
																		
																		if($.pfgmap3static.pfmapobj != null && mylatlng != null){
																			$.pfmap_recenter($.pfgmap3static.pfmapobj,mylatlng,0,0);
																		}
																	},
																	error: function (request, status, error) {},
																	complete: function(){$(".pfmaploading").fadeOut("slow");},
																	
																});
														}
													}else{
														$(".pfmaploading").fadeOut("slow");
														var mylatlng = new google.maps.LatLng('.$mylat.','.$mylng.');
														if($.pfgmap3static.pfmapobj != null && mylatlng != null){
															$.pfmap_recenter($.pfgmap3static.pfmapobj,mylatlng,0,0);
														}
													}
													
													});
												';
										}
										
										if($fieldtaxname != ''){

											$fieldtaxSelected = PFSFIssetControl('setupsearchfields_'.$slug.'_posttax_selected','','');
											
											/*Select tax auto on cat page*/
											if (isset($pfgetdata['pointfinderltypes'])) {
																				
												if ($widget == 1 && !empty($pfgetdata['pointfinderltypes'])) {
													
													global $wp_query;
													if(isset($wp_query->query_vars['taxonomy'])){
														$taxonomy_name = $wp_query->query_vars['taxonomy'];
														
														if ($taxonomy_name == 'pointfinderltypes') {
															
															$term_slug = $wp_query->query_vars['term'];
											
															$term_name = get_term_by('slug', $term_slug, $taxonomy_name,'ARRAY_A');
													
															$fieldtaxSelected = $term_name['term_id'];

														}
													}
												}
											}
											

											$fieldvalues = get_terms($fieldtaxname,array('hide_empty'=>false)); 
											
											$this->FieldOutput .= '	<option></option>';
											if($fieldtaxgroup == 1){
												/*If it is groupped*/
												foreach( $fieldvalues as $parentfieldvalue){
													if($parentfieldvalue->parent == 0){
														$this->FieldOutput .= '<optgroup label="'.$parentfieldvalue->name.'">';
															$mypf_text = '';
															if($widget == 1){
																if (isset($pfgetdata[$slug])) {
																	if ($parentfieldvalue->term_id == $pfgetdata[$slug]) {
																		$mypf_text = " selected";
																	}
																}
															}
															
															if($fieldtaxSelected != ''){
																if(strcmp($fieldtaxSelected,$parentfieldvalue->term_id) == 0){ $fieldtaxSelectedValuex = 1;}else{ $fieldtaxSelectedValuex = 0;}
															}else{
																$fieldtaxSelectedValuex = 0;
															}


															if ($fieldtaxgroup_ex == 1) {
																if ($widget == 0) {
																	if ($fieldtaxSelectedValuex == 1) {
																		$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.' selected>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																	}else{
																		$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.'>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																	}
																}else{
																	if (empty($pfgetdata)) {
																		if ($fieldtaxSelectedValuex == 1) {
																			$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.' selected>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																		}else{
																			$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.'>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																		}
																	}else{
																		if (isset($pfgetdata['pointfinderltypes'])) {
																			if ($fieldtaxSelectedValuex == 1) {
																				$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.' selected>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																			}else{
																				$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.'>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																			}
																		}else{
																			$this->FieldOutput .= '	<option value="'.$parentfieldvalue->term_id.'"'.$mypf_text.'>'.$parentfieldvalue->name.' '.esc_html__('(All)','pointfindert2d').'</option>';
																		}
																		
																		
																	}
																}
															}
															
															
															foreach( $fieldvalues as $fieldvalue){
																if($fieldvalue->parent == $parentfieldvalue->term_id){
																	if($fieldtaxSelected != ''){
																		if(strcmp($fieldtaxSelected,$fieldvalue->term_id) == 0){ $fieldtaxSelectedValue = 1;}else{ $fieldtaxSelectedValue = 0;}
																	}else{
																		$fieldtaxSelectedValue = 0;
																	}
																	
																	if($fieldtaxSelectedValue == 1 && $widget == 0){
																		$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'" selected>'.$fieldvalue->name.'</option>';
																	}else{
																		if (array_key_exists($slug,$pfgetdata)) {
																			if (isset($pfgetdata[$slug])) {
																				if ($fieldvalue->term_id == $pfgetdata[$slug]) {
																					$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'" selected>'.$fieldvalue->name.'</option>';
																				}else{
																					if ($fieldtaxSelectedValue == 1) {
																						$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'" selected>'.$fieldvalue->name.'</option>';
																					}else{
																						$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$fieldvalue->name.'</option>';
																					}
																				}
																			}else{
																				$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$fieldvalue->name.'</option>';
																			}
																			
																		}else{
																			$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$fieldvalue->name.'</option>';
																		}
																	}
																}
															}
															
														$this->FieldOutput .= '</optgroup>';
													}
												}
											}else{
												/*If it is not groupped*/
												foreach( $fieldvalues as $fieldvalue){
													if($fieldvalue->parent != 0){$hasparentitem = ' ';}else{$hasparentitem = '';}
													if($fieldtaxSelected != ''){
														if(strcmp($fieldtaxSelected,$fieldvalue->term_id) == 0){ $fieldtaxSelectedValue = 1;}else{ $fieldtaxSelectedValue = 0;}
													}else{
														$fieldtaxSelectedValue = 0;
													}
													
													if($fieldtaxSelectedValue == 1 && $widget == 0){
														$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'" selected>'.$hasparentitem.$fieldvalue->name.'</option>';
													}else{
														
														if (array_key_exists($slug,$pfgetdata)) {
															if (isset($pfgetdata[$slug])) {
																if ($fieldvalue->term_id == $pfgetdata[$slug]) {
																	$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'" selected>'.$hasparentitem.$fieldvalue->name.'</option>';
																}else{
																	$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$hasparentitem.$fieldvalue->name.'</option>';
																}
															}else{
																$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$hasparentitem.$fieldvalue->name.'</option>';
															}
															
														}else{
															$this->FieldOutput .= '	<option value="'.$fieldvalue->term_id.'">'.$hasparentitem.$fieldvalue->name.'</option>';
														}
													}
															
												}
											}
										}
										
									}elseif($rvalues_check == 1){
									/* If not a taxonomy */

										$rvalues = PFSFIssetControl('setupsearchfields_'.$slug.'_rvalues','','');

										if(count($rvalues) > 0){$fieldvalues = $rvalues;}else{$fieldvalues = '';}/* Get element's custom values.*/

										if(count($fieldvalues) > 0){
											
											$this->FieldOutput .= '	<option></option>';
											$ikk = 0;
											foreach ($fieldvalues as $s) { 

												if (function_exists('icl_t')) {
													$s = icl_t('admin_texts_theme_pointfinder', '[pfsearchfields_options][setupsearchfields_'.$slug.'_rvalues]'.$ikk, $s);
												}

												if ($pos = strpos($s, '=')) { 

													

													if($widget == 1){

														if (array_key_exists($slug,$pfgetdata)) {
															if (isset($pfgetdata[$slug])) {
																if (trim(substr($s, 0, $pos)) == $pfgetdata[$slug]) {
																	$this->FieldOutput .= '	<option value="'.trim(substr($s, 0, $pos)).'" selected>'.trim(substr($s, $pos + strlen('='))).'</option>';
																}else{
																	$this->FieldOutput .= '	<option value="'.trim(substr($s, 0, $pos)).'">'.trim(substr($s, $pos + strlen('='))).'</option>';
																}
															}else{
																$this->FieldOutput .= '	<option value="'.trim(substr($s, 0, $pos)).'">'.trim(substr($s, $pos + strlen('='))).'</option>';
															}
															
														}else{
															$this->FieldOutput .= '	<option value="'.trim(substr($s, 0, $pos)).'">'.trim(substr($s, $pos + strlen('='))).'</option>';
														}

														

													}else{
														
														$this->FieldOutput .= '	<option value="'.trim(substr($s, 0, $pos)).'">'.trim(substr($s, $pos + strlen('='))).'</option>';
															
													}
												}
												$ikk++;
											}
										}

									}

									$this->FieldOutput .= '</select>';
									$this->FieldOutput .= '</label>';
									
									if($column_type == 1){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '</div></div>';
										}else{
											if ($hormode == 1 && $widget == 0) {
												$this->FieldOutput .= '</div>';
											}
											$this->FieldOutput .= '</div></div></div>';
										}
									}else{
										if ($hormode == 1 && $widget == 0) {
											$this->FieldOutput .= '</div>';
										}
										$this->FieldOutput .= '</div>';
									};

									
								}/*Parent Check*/
							}/*Show only widget end.*/
							break;
						
						case '2':
						/* Slider Field */
						
							$showonlywidget = PFSFIssetControl('setupsearchfields_'.$slug.'_showonlywidget','','0');
							$showonlywidget_check = 'show';

							if ($showonlywidget == 0 && $widget == 0) {
								$showonlywidget_check = 'show';
							}elseif ($showonlywidget == 1 && $widget == 0) {
								$showonlywidget_check = 'hide';
							}else{
								$showonlywidget_check = 'show';
							}

							if ($showonlywidget_check == 'show') {
								
								$target = PFSFIssetControl('setupsearchfields_'.$slug.'_target','','');

								$itemparent = $this->CheckItemsParent($target);
								
								if($itemparent == 'none'){								
									
									$fieldtext = PFSFIssetControl('setupsearchfields_'.$slug.'_fieldtext','','');

									//Check price item
									$itempriceval = $this->PriceFieldCheck($target);
									
									
									//Check size item
									$itemsizeval = $this->SizeFieldCheck($target);
										
									// Get slider type.
									$slidertype = PFSFIssetControl('setupsearchfields_'.$slug.'_type','','');
									if($slidertype == 'range'){ $slidertype = 'true';}


									//Min value, max value, steps, color
									$fmin = PFSFIssetControl('setupsearchfields_'.$slug.'_min','','0');
									$fmax = PFSFIssetControl('setupsearchfields_'.$slug.'_max','','1000000');
									$fsteps = PFSFIssetControl('setupsearchfields_'.$slug.'_steps','','1');
									$fcolor = PFSFIssetControl('setupsearchfields_'.$slug.'_colorslider','','#3D637C');
									$fcolor2 = PFSFIssetControl('setupsearchfields_'.$slug.'_colorslider2','','#444444');
									$svalue = '';
									
									
									if (array_key_exists($slug,$pfgetdata)) {
										if($slidertype == 'true'){ 
											$valuestext = 'values:'.'['.$pfgetdata[$slug].'],'; 
											$slidertypetext = 'range: '.$slidertype.',';
										}
										if($slidertype == 'min'){ 
											$valuestext = 'value:'.$pfgetdata[$slug].',';
											$slidertypetext = 'range: \''.$slidertype.'\',';
										}
										if($slidertype == 'max'){ 
											$valuestext = 'value:'.$pfgetdata[$slug].',';
											$slidertypetext = 'range: \''.$slidertype.'\',';
										}
									}else{
										if($slidertype == 'true'){ 
											$valuestext = 'values:'.'['.$fmin.','.$fmax.'],'; 
											$slidertypetext = 'range: '.$slidertype.',';
										}
										if($slidertype == 'min'){ 
											$valuestext = 'value:'.$fmin.',';
											$slidertypetext = 'range: \''.$slidertype.'\',';
										}
										if($slidertype == 'max'){ 
											$valuestext = 'value:'.$fmax.',';
											$slidertypetext = 'range: \''.$slidertype.'\',';
										}
									}
									
									if($itempriceval != 'none'){
										$suffixtext = '+"'.$itempriceval['CFSuffix'].'"';
										$suffixtext2 = '+" - "';
										$prefixtext = '"'.$itempriceval['CFPrefix'].'"+';
										$prefixtext2 = '+"'.$itempriceval['CFPrefix'].'"+';
										$prefixtext3 = $itempriceval['CFPrefix'];
									}elseif($itemsizeval != 'none'){
										$suffixtext = '+"'.$itemsizeval['CFSuffix'].'"';
										$suffixtext2 = '+" - "';
										$prefixtext = '"'.$itemsizeval['CFPrefix'].'"+';
										$prefixtext2 = '+"'.$itemsizeval['CFPrefix'].'"+';
										$prefixtext3 = $itemsizeval['CFPrefix'];
									}else{
										$suffixtext = '';
										$suffixtext2 = '" - "';
										$prefixtext = '';
										$prefixtext2 = '';
										$prefixtext3 = '';
									}
									
									//Create script for this slider.

									$this->ScriptOutput .= '$( "#'.$slug.'" ).slider({'.$slidertypetext.''.$valuestext.'min: '.esc_js($fmin).',max: '.esc_js($fmax).',step: '.esc_js($fsteps).',slide: function(event, ui) {';
										
									$this->ScriptOutput .= '$("#'.$slug.'-view").';
									if($slidertype == 'true'){
										if($itempriceval != 'none'){
											$this->ScriptOutput .='val('.$prefixtext.' number_format(ui.values[0], '.$itempriceval['CFDecima'].', "'.$itempriceval['CFDecimp'].'", "'.$itempriceval['CFDecimt'].'") + " - '.$prefixtext3.'" + number_format(ui.values[1], '.$itempriceval['CFDecima'].', "'.$itempriceval['CFDecimp'].'", "'.$itempriceval['CFDecimt'].'") '.$suffixtext.');';
											
											
										}elseif($itemsizeval != 'none'){
											$this->ScriptOutput .='val('.$prefixtext.' number_format(ui.values[0], '.$itemsizeval['CFDecima'].', "'.$itemsizeval['CFDecimp'].'", "'.$itemsizeval['CFDecimt'].'") + " - '.$prefixtext3.'" + number_format(ui.values[1], '.$itemsizeval['CFDecima'].', "'.$itemsizeval['CFDecimp'].'", "'.$itemsizeval['CFDecimt'].'")  '.$suffixtext.');';
											
										}else{
											$this->ScriptOutput .='val(ui.values[0] + " - " + ui.values[1]);';
											
										}
									}else{
										if($itempriceval != 'none'){
											$this->ScriptOutput .='val('.$prefixtext.' ui.value '.$suffixtext.');';
											
										}elseif($itemsizeval != 'none'){
											$this->ScriptOutput .='val('.$prefixtext.' ui.value '.$suffixtext.');';
											
										}else{
											$this->ScriptOutput .='val(ui.value);';
											
										}
									}
									
									$this->ScriptOutput .= '$("#'.$slug.'-view2").';
									if($slidertype == 'true'){
										$this->ScriptOutput .='val(ui.values[0]+","+ui.values[1]);';
									}else{
										$this->ScriptOutput .='val(ui.value);';
									}
									
									
									
									
									$this->ScriptOutput .='}});';
									
									$this->ScriptOutput .='$( "#'.$slug.'" ).addClass("ui-slider-'.$slug.'");';
									
									if($slidertype == 'true'){
										if($itempriceval != 'none'){
											$this->ScriptOutput .='$("#'.$slug.'-view").val('.$prefixtext.' number_format($("#'.$slug.'").slider("values",0), '.$itempriceval['CFDecima'].', "'.$itempriceval['CFDecimp'].'", "'.$itempriceval['CFDecimt'].'") '.$suffixtext2.''.$prefixtext2.'number_format($("#'.$slug.'").slider("values",1), '.$itempriceval['CFDecima'].', "'.$itempriceval['CFDecimp'].'", "'.$itempriceval['CFDecimt'].'") '.$suffixtext.');';
										}elseif($itemsizeval != 'none'){
											$this->ScriptOutput .='$("#'.$slug.'-view").val('.$prefixtext.' number_format($("#'.$slug.'").slider("values", 0), '.$itemsizeval['CFDecima'].', "'.$itemsizeval['CFDecimp'].'", "'.$itemsizeval['CFDecimt'].'")  '.$suffixtext2.''.$prefixtext2.' number_format($("#'.$slug.'").slider("values", 1), '.$itemsizeval['CFDecima'].', "'.$itemsizeval['CFDecimp'].'", "'.$itemsizeval['CFDecimt'].'") '.$suffixtext.');';
										}else{
											$this->ScriptOutput .='$("#'.$slug.'-view").val($("#'.$slug.'").slider("values", 0) + " - " + $("#'.$slug.'").slider("values", 1));';
										}
									}else{
										if($itempriceval != 'none'){
											$this->ScriptOutput .='$("#'.$slug.'-view").val( '.$prefixtext.' number_format($("#'.$slug.'").slider("value"), '.$itempriceval['CFDecima'].', "'.$itempriceval['CFDecimp'].'", "'.$itempriceval['CFDecimt'].'") '.$suffixtext.');';
										}elseif($itemsizeval != 'none'){
											$this->ScriptOutput .='$("#'.$slug.'-view").val( '.$prefixtext.' number_format($("#'.$slug.'").slider("value"), '.$itemsizeval['CFDecima'].', "'.$itemsizeval['CFDecimp'].'", "'.$itemsizeval['CFDecimt'].'") '.$suffixtext.');';
										}else{
											$this->ScriptOutput .='$("#'.$slug.'-view").val( $("#'.$slug.'").slider("value"));';
										}
									}
									
									
									$this->ScriptOutputDocReady .= '$(document).one("ready",function(){$.pfsliderdefaults.fields["'.$slug.'_main"] = $("#'.$slug.'-view").val()});';
									
									$column_type = PFSFIssetControl('setupsearchfields_'.$slug.'_column','','0');						
									
									

									if($column_type == 1){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '<div class="col6 last">';
										}else{
											if ($hormode == 1 && $widget == 0) {
												$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
											}
											$this->FieldOutput .= '<div class="row"><div class="col6 first">';
										}
										$this->PFHalf++;
									}else{
										if ($hormode == 1 && $widget == 0) {
											$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
										}
									};
									
									//Slider size calculate
									if(strlen($fmax) <=3){
										$slidersize = ((strlen($fmax)*8))+10;
									}else{
										if($suffixtext != ''){
											$slidersize = ((strlen($fmax)*8)*2)+50;
										}else{
											$slidersize = ((strlen($fmax)*8)*2)+20;
										}
									}
									//Output for this field
									$this->FieldOutput .= ' <div id="'.$slug.'_main"><label for="'.$slug.'-view" class="pfrangelabel">'.$fieldtext.'</label>
															<input type="text" id="'.$slug.'-view" class="slider-input" style="width:'.$slidersize.'px" disabled>';
									
									$this->FieldOutput .= '<input name="'.$slug.'" id="'.$slug.'-view2" type="hidden" class="pfignorevalidation" value="">';
									
									$this->FieldOutput .= ' <div class="slider-wrapper">
																<div id="'.$slug.'"></div>  
															</div></div>';
									if($column_type == 1){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '</div>';
										}else{
											if ($hormode == 1 && $widget == 0) {
												$this->FieldOutput .= '</div>';
											}
											$this->FieldOutput .= '</div></div>';
										}
									}else{
										if ($hormode == 1 && $widget == 0) {
											$this->FieldOutput .= '</div>';
										}
									};
									

									
									if (array_key_exists($slug,$pfgetdata)) {
										$this->ScriptOutput .= '$( "#'.$slug.'-view2" ).val("'.$pfgetdata[$slug].'");';
									}
								}
							}
							break;
						
						case '4':
						/* Text Field */
							
							$showonlywidget = PFSFIssetControl('setupsearchfields_'.$slug.'_showonlywidget','','0');
							$showonlywidget_check = 'show';

							if ($showonlywidget == 0 && $widget == 0) {
								$showonlywidget_check = 'show';
							}elseif ($showonlywidget == 1 && $widget == 0) {
								$showonlywidget_check = 'hide';
							}else{
								$showonlywidget_check = 'show';
							}
							if ($showonlywidget_check == 'show') {
								
								$target = PFSFIssetControl('setupsearchfields_'.$slug.'_target_target','','');

								$itemparent = $this->CheckItemsParent($target);
								
								if($itemparent == 'none'){

									$validation_check = PFSFIssetControl('setupsearchfields_'.$slug.'_validation_required','','0');
									$field_autocmplete = PFSFIssetControl('setupsearchfields_'.$slug.'_autocmplete','','1');

									if($validation_check == 1){
										$validation_message = PFSFIssetControl('setupsearchfields_'.$slug.'_message','','');
										
										if($this->VSOMessages != ''){
											$this->VSOMessages .= ','.$slug.':"'.$validation_message.'"';
										}else{
											$this->VSOMessages = $slug.':"'.$validation_message.'"';
										}
										
										if($this->VSORules != ''){
											$this->VSORules .= ','.$slug.':"required"';
										}else{
											$this->VSORules = $slug.':"required"';
										}
									}
									
									$fieldtext = PFSFIssetControl('setupsearchfields_'.$slug.'_fieldtext','','');
									$placeholder = PFSFIssetControl('setupsearchfields_'.$slug.'_placeholder','','');
									$column_type = PFSFIssetControl('setupsearchfields_'.$slug.'_column','','0');

									$geolocfield = PFSFIssetControl('setupsearchfields_'.$slug.'_geolocfield','','0');
									$geolocfield = ($geolocfield == 1)? 'Mile':'Km';
									$geolocfield2 = PFSFIssetControl('setupsearchfields_'.$slug.'_geolocfield2','','100');
									
									

									if($column_type == 1 && $target != 'google'){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '<div class="col6 last">';
										}else{
											if ($hormode == 1 && $widget == 0 && $target != 'google') {
												$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
											}
											$this->FieldOutput .= '<div class="row"><div class="col6 first">';
										}
										$this->PFHalf++;
									}else{
										if ($hormode == 1 && $widget == 0 && $target != 'google') {
											$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
										}
									};

									if (array_key_exists($slug,$pfgetdata)) {
										$valtext = ' value = "'.$pfgetdata[$slug].'" ';;
									}else{
										$valtext = '';
									}

									
									if ($target == 'google') {
										if ($widget == 0) {
											
											if ($hormode == 1) {
												$this->FieldOutput .= '<div class="col-lg-3 col-md-4 col-sm-4 colhorsearch">';
											}
											$this->FieldOutput .= '
											<div id="'.$slug.'_main">
												<label for="'.$slug.'" class="pftitlefield">'.$fieldtext.'</label>
												<label class="pflabelfixsearch lbl-ui search">
													<input type="search" name="'.$slug.'" id="'.$slug.'" class="input" placeholder="'.$placeholder.'"'.$valtext.' />
													<input type="hidden" name="pointfinder_google_search_coord" id="pointfinder_google_search_coord" class="input" value="" />
													<input type="hidden" name="pointfinder_google_search_coord_unit" id="pointfinder_google_search_coord_unit" class="input" value="'.$geolocfield.'" />
													<a class="button" id="pf_search_geolocateme" title="'.esc_html__('Locate me!','pointfindert2d').'"><img src="'.get_template_directory_uri().'/images/geoicon.svg" width="16px" height="16px" class="pf-search-locatemebut" alt="'.esc_html__('Locate me!','pointfindert2d').'"><div class="pf-search-locatemebutloading"></div></a>
												</label> 
											';
											if ($hormode == 1) {
												$this->FieldOutput .= '</div></div><div class="col-lg-3 col-md-4 col-sm-4 colhorsearch"><div id="'.$slug.'_main_ex">';
											}
											$this->FieldOutput .= '
												<div id="pointfinder_radius_search_main">
													<label for="pointfinder_radius_search-view" class="pfrangelabel">'.esc_html__('Distance','pointfindert2d').' ('.$geolocfield.') :</label>
													<input type="text" id="pointfinder_radius_search-view" class="slider-input" disabled="" style="width: 44%;"">
													<input name="pointfinder_radius_search" id="pointfinder_radius_search-view2" type="hidden" class="pfignorevalidation"> 
													<div class="slider-wrapper">
														<div id="pointfinder_radius_search" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all ui-slider-pointfinder_radius_search">
															<div class="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-min"></div>
															<span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0"></span>
														</div>  
													</div>
												</div> 

											</div>                        
											';
											if ($hormode == 1) {
												$this->FieldOutput .= '</div>';
											}
											$this->ScriptOutput .= "
											$('#pf_search_geolocateme').on('click',function(){
												$('.pf-search-locatemebut').hide('fast'); $('.pf-search-locatemebutloading').show('fast');
												$.pfgeolocation_findme('".$slug."');
												return false;
											});
											
											$(document).ready(function(){
												setTimeout(function(){
												var map = $('#wpf-map').gmap3('get');
												var input = (document.getElementById('".$slug."'));
												
												
												/*$('#".$slug."').on('keyup',function(e) {
												  if (e.keyCode == 13) {               
												    e.preventDefault();
												    return false;
												  }
												});*/
												
											
													var autocomplete = new google.maps.places.Autocomplete(input);
													autocomplete.bindTo('bounds', map);
													
													google.maps.event.addListener(autocomplete, 'place_changed', function() {
													    var place = autocomplete.getPlace();
													    if (!place.geometry) {
													      return;
													    }
														$('#pointfinder_google_search_coord').val(place.geometry.location.lat()+','+place.geometry.location.lng());
													});
													},1000);
													
											});
											";

											$this->ScriptOutput .= '
												$( "#pointfinder_radius_search" ).slider({
													range: "min",value:10,min: 0,max: '.$geolocfield2.',step: 1,
													slide: function(event, ui) {
														$("#pointfinder_radius_search-view").val(ui.value);
														$("#pointfinder_radius_search-view2").val(ui.value);
													}
												});

												$("#pointfinder_radius_search-view").val( $("#pointfinder_radius_search").slider("value"));

																
												$(document).one("ready",function(){
													$("#pointfinder_radius_search-view2").val(10);
												});
											';
										}

									}elseif ($target == 'title' || $target == 'address') {
										
										$this->FieldOutput .= '
										<div id="'.$slug.'_main" class="ui-widget">
										<label for="'.$slug.'" class="pftitlefield">'.$fieldtext.'</label>
										<label class="lbl-ui pflabelfixsearch pflabelfixsearch'.$slug.'">
											<input type="text" name="'.$slug.'" id="'.$slug.'" class="input" placeholder="'.$placeholder.'"'.$valtext.' />
										</label>    
										</div>                        
										';
										
										if($field_autocmplete == 1){
											$this->ScriptOutput .= '
											$( "#'.$slug.'" ).bind("keydown",function(){


											$( "#'.$slug.'" ).autocomplete({
											  appendTo: ".pflabelfixsearch'.$slug.'",
										      source: function( request, response ) {
										        $.ajax({
										          url: theme_scriptspf.ajaxurl,
										          dataType: "jsonp",
										          data: {
										          	action: "pfget_autocomplete",
										            q: request.term,
										            security: theme_scriptspf.pfget_autocomplete,
										            ftype: "'.$target.'"
										          },
										          success: function( data ) {
										            response( data );
										          }
										        });
										      },
										      minLength: 3,
										      select: function( event, ui ) {
										        $("#'.$slug.'").val(ui.item);
										      },
										      open: function() {
										        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
										      },
										      close: function() {
										        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
										      }
										    });

											});
											';
										}


									} else {
										
										$this->FieldOutput .= '
										<div id="'.$slug.'_main">
										<label for="'.$slug.'" class="pftitlefield">'.$fieldtext.'</label>
										<label class="lbl-ui pflabelfixsearch">
											<input type="text" name="'.$slug.'" id="'.$slug.'" class="input" placeholder="'.$placeholder.'"'.$valtext.' />
										</label>    
										</div>                        
										';
										
									}
									
									
									if($column_type == 1 && $target != 'google'){
										if ($this->PFHalf % 2 == 0) {
											$this->FieldOutput .= '</div>';
										}else{
											if ($hormode == 1 && $widget == 0 && $target != 'google') {
												$this->FieldOutput .= '</div>';
											}
											$this->FieldOutput .= '</div></div>';
										}
									}else{
										if ($hormode == 1 && $widget == 0 && $target != 'google') {
											$this->FieldOutput .= '</div>';
										}
									};
									
									
								}
							}
							
						break;
					}
					
					
		}

				
	}
}
?>