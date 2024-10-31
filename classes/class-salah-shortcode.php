<?php

if ( ! class_exists( 'SalahShortcode' ) ) {
	
	class SalahShortcode {
		public $cleanup;
		
		function __construct(){
			add_filter( 'the_content', array( $this, 'shortcodes_formatter' ));
			add_filter( 'widget_text', array( $this, 'shortcodes_formatter' ));
			add_shortcode( 'salah_time', array( $this, 'shortcode_salah_time' ) );
			add_shortcode( 'st_date_english', array( $this, 'shortcode_st_date_english' ) );
			add_shortcode( 'st_date_hijri', array( $this, 'shortcode_st_date_hijri' ) );	
		}
	
		public function shortcodes_formatter($content) {
			$block = join("|",array("salah_time", "st_date_english", "st_date_hijri"));
		
			// opening tag
			$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
		
			// closing tag
			$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/","[/$2]",$rep);
		
			return $rep;
		}
		
		//////////////////////////////////////////////////////////////////
		// shortcode_st_date_english Shortcode
		//////////////////////////////////////////////////////////////////
		function shortcode_st_date_english($atts, $content = null) {
			
			extract( shortcode_atts( array(
				'format'		 	=> 'd F, Y',
			), $atts ) );
		
			echo '<span class="date-english">'.date(esc_attr($format)).'</span>';	
		}
		
		//////////////////////////////////////////////////////////////////
		// shortcode_st_date_hijri Shortcode
		//////////////////////////////////////////////////////////////////
		function shortcode_st_date_hijri($atts, $content = null) {
			
			
			wp_enqueue_script( 'hijri-date' );
		
			extract( shortcode_atts( array(
				'format'		 	=> 'd F, Y',
			), $atts ) );
			
			
		?>
        <span id="hijri_date" class="date-hijri"></span>
		<script type="text/javascript">
            ;(function ($) {
				"use strict";
				jQuery(document).ready(function ($) {
					var hijri_date = madinaHijriDate();
					document.getElementById("hijri_date").innerHTML = hijri_date;
				})
			}(jQuery));							
		</script>
		<?php				
		}
						
		//////////////////////////////////////////////////////////////////
		// salah_time_table Shortcode
		//////////////////////////////////////////////////////////////////
		function shortcode_salah_time($atts, $content = null) {
			
			
			wp_enqueue_script( 'PrayTimes' );
		
			extract( shortcode_atts( array(
				'location'	=> '23.798632, 90.353143',
				'method'	=> 'ISNA',
				'layout'	=> "calendar-jamat", // 0, 1, 2, 3, 4 -- 3,4 = calendar
				'calender_type'	=> 'cal_light', // cal_dark
			), $atts ) );
									
						
		?>
        <?php if($location): ?>
        <div class="prayer-time-wrap">
            <div class="prayer-time-calender <?php echo esc_attr($calender_type); ?>">
                <div class="salah-calender-selector text-center">
                <div class="salah-row m-zero">
                    <div class="salah-col-md-3 salah-col-sm-3 salah-col-xs-3 p-zero"><div class="calender-selector accent-color" id="lpe_displayMonth_left" data-event="lpe_displayMonth_left">&lt;&lt;</div></div>
                    <div class="salah-col-md-6 salah-col-sm-6 salah-col-xs-6 p-zero"><div id="lpe_month_title" class="calender-caption"></div></div>
                    <div class="salah-col-md-3 salah-col-sm-3 salah-col-xs-3 p-zero"><div class="calender-selector accent-color" id="lpe_displayMonth_right" data-event="lpe_displayMonth_right">&gt;&gt;</div></div>
                </div>
                </div>
                <div class="prayer-time-table">
                <table class="table-bordered" id="timetable">
                    <tbody></tbody>
                </table>
                </div>
            </div>
		<script type="text/javascript">
            ;(function($) {	
				'use strict';				
				jQuery(document).ready(function ($) {
					function madinagmod(n,m){
						return ((n%m)+m)%m;
					}
					
					function HijriCalendar(adjust){
						var today = new Date();
						if(adjust) {
							var adjustmili = 1000*60*60*24*adjust; 
							var todaymili = today.getTime()+adjustmili;
							today = new Date(todaymili);
						}
						var day = today.getDate();
						var month = today.getMonth();
						var year = today.getFullYear();
						var m = month+1;
						var y = year;
						if(m < 3) {
							y -= 1;
							m += 12;
						}
					
						var a = Math.floor(y/100.);
						var b = 2-a+Math.floor(a/4.);
						if(y < 1583) b = 0;
						if(y==1582) {
							if(m > 10)  b = -10;
							if(m==10) {
								b = 0;
								if(day > 4) b = -10;
							}
						}
					
						var jd = Math.floor(365.25*(y+4716))+Math.floor(30.6001*(m+1))+day+b-1524;
					
						b = 0;
						if(jd>2299160){
							a = Math.floor((jd-1867216.25)/36524.25);
							b = 1+a-Math.floor(a/4.);
						}
						var bb = jd+b+1524;
						var cc = Math.floor((bb-122.1)/365.25);
						var dd = Math.floor(365.25*cc);
						var ee = Math.floor((bb-dd)/30.6001);
						day =(bb-dd)-Math.floor(30.6001*ee);
						month = ee-1;
						if(ee>13) {
							cc += 1;
							month = ee-13;
						}
						year = cc-4716;
					
						if(adjust) {
							var wd = madinagmod(jd+1-adjust,7)+1;
						} else {
							var wd = madinagmod(jd+1,7)+1;
						}
					
						var iyear = 10631./30.;
						var epochastro = 1948084;
						var epochcivil = 1948085;
					
						var shift1 = 8.01/60.;
						
						var z = jd-epochastro;
						var cyc = Math.floor(z/10631.);
						z = z-10631*cyc;
						var j = Math.floor((z-shift1)/iyear);
						var iy = 30*cyc+j;
						z = z-Math.floor(j*iyear+shift1);
						var im = Math.floor((z+28.5001)/29.5);
						if(im==13) im = 12;
						var id = z-Math.floor(29.5001*im-29);
					
						var myRes = new Array(8);
					
						myRes[0] = day; //calculated day (CE)
						myRes[1] = month-1; //calculated month (CE)
						myRes[2] = year; //calculated year (CE)
						myRes[3] = jd-1; //julian day number
						myRes[4] = wd-1; //weekday number
						myRes[5] = id; //islamic date
						myRes[6] = im-1; //islamic month
						myRes[7] = iy; //islamic year
					
						return myRes;
					}
					function HijriDate(adjustment) {
						var wdNames = new Array("Ahad","Ithnin","Thulatha","Arbaa","Khams","Jumuah","Sabt");
						var iMonthNames = new Array("Muharram","Safar","Rabi'al-awwal","Rabi'al-thani",
						"Jumadal al-awwal","Jumadal al-thani","Rajab","Sha'ban",
						"Ramadan","Shawwal","Dhul Qa'ada","Dhul Hijja");
						var iDate = HijriCalendar(adjustment);
						var outputIslamicDate = iDate[5] + " " + iMonthNames[iDate[6]] + " " + iDate[7];
						return outputIslamicDate;
					}
					function format(v) { return ('0' + v).slice(-2); }
					function add(time, minutes) {
							var m = time.split(':').reduce(function (h, m) {  return 60 * h + (+m); });
							m += minutes;
							return [Math.floor(m / 60) % 24, m % 60].map(format).join(':');
					}
					function ampm(t) {
						var v = t.split(':');
						return [v[0] % 12 || 12, v[1]].map(format).join(':') + (v[0] < 12 ? ' AM' : ' PM');
					}
					function to12(t) {
						var v = t.split(':');
						return [v[0] % 12 || 12, v[1]].map(format).join(':') + (v[0] < 12 ? '' : '');
					}
					function to24(t) {
						var v = t.split(':');
						if(parseInt(v[0]) == 12){
							return [12, v[1]].map(format).join(':');
						}else{
							return [parseInt(v[0]) + 12, v[1]].map(format).join(':');
						}
					}
					function parseDate(time){
						var today = new Date();
						var dd = today.getDate();
						var mm = today.getMonth()+1; //January is 0!
						
						var yyyy = today.getFullYear();
						if(dd < 10){
							dd='0'+dd;
						} 
						if(mm < 10){
							mm='0'+mm;
						} 

						return mm+' '+dd+', '+yyyy+' '+time;
					}
					function yesterDate(time, yesto){
						var today = new Date();
						var yesterday = new Date(today);
						if(yesto == 'to'){
							yesterday.setDate(today.getDate() + 1);
						}else{
							yesterday.setDate(today.getDate() - 1);
						}
			
						var dd = yesterday.getDate();
						var mm = yesterday.getMonth()+1; //January is 0!
						
						var yyyy = yesterday.getFullYear();
						if(dd < 10){
							dd='0'+dd;
						} 
						if(mm < 10){
							mm='0'+mm;
						} 

						return mm+' '+dd+', '+yyyy+' '+time;
					}
					
					function previousWakt(wakt){
						if(wakt == 'Fajr'){
							return 'Isha';                
						}else if(wakt == 'Dhuhr'){
							return 'Fajr';
						}else if(wakt == 'Asr'){
							return 'Dhuhr';
						}else if(wakt == 'Maghrib'){
							return 'Asr';
						}else if(wakt == 'Isha'){
							return 'Maghrib';
						}
					}
					function currentTime(){
						var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
						var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
						var Time = hours + ":" + minutes;
						return Date.parse(parseDate(Time));
					}  
					           
					var currentDate = new Date();
					var timeFormat = 1; 
					switchFormat(0);
					function gid(id) {
						return document.getElementById(id);
					}
					function cE(id) {
						return document.createElement(id);
					}
					// display monthly timetable
					function displayMonth(offset) {
						<?php 
							$locations = explode(',', $location); 
						?>
						var lat = <?php echo trim($locations[0]); ?>;
						var lon = <?php echo trim($locations[1]); ?>;
						var timeZone = 'auto';
						var dst = 'auto';						
						prayTimes.setMethod('<?php echo esc_attr($method); ?>');
						currentDate.setMonth(currentDate.getMonth()+ 1* offset);
						var month = currentDate.getMonth();
						var year = currentDate.getFullYear();
						var title = monthFullName(month)+ ' '+ year;
						gid('lpe_month_title').innerHTML = title;
						makeTable(year, month, lat, lon, timeZone, dst);
					}
				
					// make monthly timetable
					function makeTable(year, month, lat, lon, timeZone, dst) {	
						
						<?php if( $layout == "calendar-jamat" ) { ?>
								var items = {day: 'Day', fajr_jamat: 'Fajr', sunrise: 'Sunrise', dhuhr_jamat: 'Dhuhr', asr_jamat: 'Asr', sunset: 'Sunset', maghrib_jamat: 'Maghrib', isha_jamat: 'Isha'};
						<?php }else{ ?>
								var items = {day: 'Day', fajr: 'Fajr', fajr_jamat: 'Jamat', sunrise: 'Sunrise', dhuhr: 'Dhuhr', dhuhr_jamat: 'Jamat', asr: 'Asr', asr_jamat: 'Jamat', sunset: 'Sunset', maghrib: 'Maghrib', maghrib_jamat: 'Jamat', isha: 'Isha', isha_jamat: 'Jamat'};
						<?php } ?>
						var fajr_add_min = 0, 
							maghrib_add_min = 0; 
						
						
						var tbody = cE('tbody');
						tbody.appendChild(makeHeaderTableRow(items, items, 'head-row'));
				
						var date = new Date(year, month, 1);
						var endDate = new Date(year, month+ 1, 1);
						var format = '12hNS'; //'24h';
						var jamat_json = [];
						if(salah_conf){
							var data = salah_conf.salahTime;
							for(var i in data){
								if(data[i]['Date']){
									var newdate= data[i]['Date'].replace('-','');
									jamat_json[newdate] = {
										Fajr: data[i]['Fajr'],
										Dhuhr: data[i]['Dhuhr'], 
										Asr: data[i]['Asr'], 
										Maghrib: data[i]['Maghrib'], 
										Isha: data[i]['Isha']
									};
								}
							}

							while (date < endDate) {
								var times = prayTimes.getTimes(date, [lat,lon], timeZone, dst, format);
								var jday = date.getDate();
								jday = (jday < 10) ? '0'+ jday : jday;
								var monthNames=new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
								var jdate = monthNames[date.getMonth()]+ jday;
								times.day = jday;
								
								if(fajr_add_min != 0){
									times.fajr_jamat = add(times['fajr'], fajr_add_min);
								}else{
									times.fajr_jamat = to12(jamat_json[jdate].Fajr);
								}
								times.dhuhr_jamat = to12(jamat_json[jdate].Dhuhr);
								times.asr_jamat = to12(jamat_json[jdate].Asr);
								if(maghrib_add_min != 0){
									times.maghrib_jamat = to12(add(times['maghrib'], fajr_add_min));
								}else{
									times.maghrib_jamat = to12(jamat_json[jdate].Maghrib);
								}
								times.isha_jamat = to12(jamat_json[jdate].Isha);
			
								var today = new Date(); 
								var isToday = (date.getMonth() == today.getMonth()) && (date.getDate() == today.getDate());
								var klass = isToday ? 'today-row' : '';
								tbody.appendChild(makeTableRow(times, items, klass));
								date.setDate(date.getDate()+ 1);  // next day
							}
							removeAllChild(gid('timetable'));
							gid('timetable').appendChild(tbody);
						}
						
					}
					// make a table row
					function makeTableRow(data, items, klass) {
						var row = cE('tr');
						for (var i in items) {
							var cell = cE('td');
							cell.innerHTML = data[i];
							row.appendChild(cell);
						}
						row.className = klass;
						return row;		
					}
					function makeHeaderTableRow(data, items, klass) {
						var row = cE('tr');
						for (var i in items) {
							var cell = cE('td');
							cell.innerHTML = data[i];
							row.appendChild(cell);
						}
						row.className = klass;
						return row;		
					}
				
					// remove all children of a node
					function removeAllChild(node) {
						if (node == undefined || node == null)
							return;
						while (node.firstChild)
							node.removeChild(node.firstChild);
					}
										
					// update table
					function update() {
						displayMonth(0);
					}
					
					function switchFormat(offset) {
						var formats = ['24-hour', '12-hour'];
						timeFormat = (timeFormat+ offset)% 2;
						update();
					}
										
					// return month full name
					function monthFullName(month) {
						var monthName = new Array('January', 'February', 'March', 'April', 'May', 'June', 
										'July', 'August', 'September', 'October', 'November', 'December');
						return monthName[month];
					}
				
					
					jQuery(document.body).on('click', '#lpe_displayMonth_right', function(event) {
						displayMonth(+1);
					});
					jQuery(document.body).on('click', '#lpe_displayMonth_left', function(event) {
						displayMonth(-1);
					});
				});
			}(jQuery));
        </script>
        </div>
        <?php endif; ?>
		<?php
		
		}
		
	}
}

new SalahShortcode();

?>