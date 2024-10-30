<?php

/*
 * Help file.
 * Contains all the help strings for the admin area. These strings are loaded via AJAX.
 * This file is loaded with include_once into the menu class to keep the class file clean.
 *
 */ 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* 
 * global image path for the help file images.
 */

$img_url = ISRC_PLUGIN_URL . '/admin/help_files/img/';

$return_data = array();

/* 
 * Help info for: include post types. 
 */

$return_data['help_1']['html'] = __('Select the post types to index for search results. Select only for visitors visible post types. Some plugins/themes add post types only for administration use. Only selected post types will be visible in search results.','i_search');

/* 
 * Help info for: Include in search algorithm.
 */

$return_data['help_2']['html'] = __('Select Tags which will be included in search algorithm. if a post have connections with this tags. This tag will also be included in algorithm. Lets assume you have a post with "adidas, nike" tags or a category named "women clothes". If a user searched for "adidas" or "nike" or "women clothes" then this posts will be included in search results.','i_search');

/* 
 * Help info for: Enable Live Search. 
 */

$return_data['help_3']['html'] = __('Enable live search in front-end.','i_search'); 
$return_data['help_3']['img'] = $img_url.'help_live_search.jpg'; 
$return_data['help_3']['img_h'] = 250; 

/* 
 * Help info for: Replace WP intern search engine with i-Search
 */

$return_data['help_4']['html'] = __('WP do not recognize the search terms from i-Search. If enabled WP will only use i-Search terms as search results.','i_search'); 

/* 
 * Help info for: Enable logging for search analysis 
 */

$return_data['help_5']['html'] = __('i-Search will log every not found results. In the analysis tab, you can analyse and do some actions for the searched string.','i_search'); 

/* 
 * Help info for: Replace WP search form 
 */

$return_data['help_6']['html'] = __('Replace the default WP search input field in front-end with i-Search live search input field. If disabled, you need to manually insert the search form with PHP or shortcode. Check the documentation for more info about shortcode usage and PHP functions.','i_search'); 


/* 
 * Help info for: Boost up search speed 
 */

$return_data['help_7']['html'] = __('by default i-Search will use the WP intern wp-ajax function. This function is loading the whole WP framework. By enabling the boost function all this process will be skipped and save you a lot of time and server capacities.','i_search'); 

/* 
 * Help info for: Search label 
 */

$return_data['help_8']['html'] = __('The placeholder in the search field.','i_search'); 
$return_data['help_8']['img'] = $img_url.'search_label.jpg'; 
$return_data['help_8']['img_h'] = 100; 

/* 
 * Help info for: Enable submit button 
 */

$return_data['help_9']['html'] = __('Show/Hide the submit button near the search field. 
By default its disabled because by hitting the ENTER key the user is redirected to the WP default search.','i_search'); 
$return_data['help_9']['img'] = $img_url.'submit_label.jpg'; 
$return_data['help_9']['img_h'] = 50; 

/* 
 * Help info for: No results label 
 */

$return_data['help_10']['html'] = __('If no results found show this string. Only if popularity index is disabled or popular searches are not yet available.','i_search');
$return_data['help_10']['img'] = $img_url.'no_results1.jpg'; 
$return_data['help_10']['img_h'] = 150; 

/* 
 * Help info for: No results label 
 */

$return_data['help_11']['html'] = __('If a did you mean result is available show this label before the string.','i_search'); 
$return_data['help_11']['img'] = $img_url.'didumean.jpg'; 
$return_data['help_11']['img_h'] = 120; 

/* 
 * Help info for: Max number of results 
 */

$return_data['help_12']['html'] = __('Maximum suggestions to show for every selected post type / taxonomy.','i_search');
$return_data['help_12']['img'] = $img_url.'max_results.jpg'; 
$return_data['help_12']['img_h'] = 250; 

/* 
 * Help info for: Max. height of the suggestion container 
 */

$return_data['help_13']['html'] = __('Maximum height of the suggestion container. 
<strong>Automatic calculation:</strong> the container height will fit in the screen with scrollbar.
<strong>Maximum:</strong> the maximum height will be used to avoid scrollbars in the container. Some templates maybe have problems with this option.
<strong>Custom value:</strong> enter a custom height of the container. If the content not fit in the given height the rest will be scrollable.','i_search'); 

/* 
 * Help info for: Enable tabs in results 
 */

$return_data['help_14']['html'] = __('Suggestions ordered by TABS.','i_search'); 
$return_data['help_14']['img'] = $img_url.'enable_tabs.gif';
$return_data['help_14']['img_h'] = 300; 

/* 
 * Help info for: Order of the tabs 
 */

$return_data['help_15']['html'] = __('You can change the order of the tabs by dragn drop. You can also add taxonomies to the tabs. This taxonomies will be indexed for suggestions. If you changed the post types inclusion and can not see it in the tabs, save your settings first  then selected post types will be available in the tabs.','i_search'); 
$return_data['help_15']['img'] = $img_url.'enable_tabs.gif';
$return_data['help_15']['img_h'] = 300; 

/* 
 * Help info for: Show thumbnails 
 */

$return_data['help_16']['html'] = __('Shows thumbnails in suggestions. If enabled you can set the thumbnails height and width. i-Search will create the thumbnails on the fly if they not exists in the selected dimensions and delete old (from i-Search created) thumbnails if you change the size to keep your folders clean.','i_search');

/* 
 * Help info for: Show excerpt 
 */

$return_data['help_17']['html'] = __('Shows the post excerpt in the suggestions. If no excerpt is available an excerpt will be generated from the post content. The excerpt is always one line long (clean template) and will be truncated with 3 dots.','i_search');
$return_data['help_17']['img'] = $img_url.'show_excerpt.jpg'; 
$return_data['help_17']['img_h'] = 300; 

/* 
 * Help info for: View all label 
 */
 
$return_data['help_18']['html'] = __('A View All text will appear in the bottom of the suggestions to redirect the user to the WP search.','i_search');
$return_data['help_18']['img'] = $img_url.'view_all.jpg'; 
$return_data['help_18']['img_h'] = 250; 

/* 
 * Help info for: Meta Keys in search algorithm
 */
 
$return_data['help_19']['html'] = __('Here you see all of the meta keys in your WP. This does not mean that every post use this meta keys. Include meta keys which you want to be included in the search algorithm. Lets assume you have a post with a meta key "manufacturer" and the meta value of "manufacturer" is Sennheiser. If you include the "manufacturer" meta key, a search for "Sennheiser" will now include all post with "Sennheiser" as the meta value of "manufacturer".','i_search');

/* 
 * Help info for: Include taxonomies in search algorithm
 */
 
$return_data['help_20']['html'] = __('Here you see all of the taxonomies in your WP. This does not mean that every post use this taxonomies. <br>Include taxonomies which you want to be included in the search algorithm. <br>Lets assume you have a post with a taxonomy "manufacturer" and connected taxonomy value of "manufacturer" is Sennheiser. <br>If you include the "manufacturer" taxonomy, a search result for "Sennheiser" will now include all post with "Sennheiser" as taxonomy value of "manufacturer".','i_search');

/* 
 * Help info for: Exclude words from search algorithm
 */
 
$return_data['help_21']['html'] = __('This words are always excluded from the search algorithm. For example you have a tag which is almost present in every post. Here you can enter the words you want to exclude. Wildcards are allowed. If you have the tag "blog posts" and "bloger posts" almost in every post. If you now search for "blog" you will not get a good search result. You can add <code>blog*</code> as a "bad word" here and everything beginning with "blog" will be excluded from the search results.','i_search');

/* 
 * Help info for: Delete data 
 */
 
$return_data['help_22']['html'] = __('If you delete i-Search all the data from i-Search will be deleted. All the settings, all the database entries from i-Search. This action is only for deleting the plugin and not deactivating the plugin. On deactivate and activate the plugin the data will not be deleted. Only on completely deleting from the plugins menu.','i_search'); 

/* 
 * Help info for: Exclude words from search algorithm
 */
 
$return_data['help_23']['html'] = __('This words are always excluded from logging. For example someone is always searching for "asdasd". i-Search tries to detect spam. If a user with the same ip address gets no search results over 15 times in a day than the users ip address will be marked as spam and i-Search will no longer log this users not found results for 24 Hours. The user will still get search results (if found) of course. If you want to exclude continuously repeating same logs, here you can enter the "Bad Words". Wild cards are allowed. For example: If you enter only <code>asd</code> without a wildcard, only "asd" will be excluded and not "asdasd". If you enter <code>*asd*</code> as a bad word everything beginning and ending with "asd" will no longer logged.','i_search');

/* 
 * Help info for: Analysis table 
 */
 
$return_data['help_24']['html'] = __('In this table you will see all the search strings with no results.
<strong>Searched string:</strong> This string will be updated if a similar word is searched. Example: a user searched for "shoe" and get no results. >Here you will see the word "shoe" if you did not make any actions for "shoe" and a user searched for "shoes" and still get no results than the "shoe" word in the table will be updated with "shoes".
<strong>Flow:</strong> Example: If a user searched for "shoes" without results and than searched for "Nike" with search results and clicked "Nike Air Max 270" you will see in the flows the word "Nike" and in brackets the clicked search result "Nike Air Max 270".
<strong>Action:</strong> You will see here the "Did you mean" string if you entered a string.
<strong>Count:</strong> How many users from different ip addresses searched for this string.
<strong>Time:</strong> The latest log time.
<strong>Latest IP:</strong> The latest users ip address.
<strong>Edit Button:</strong> You can make some actions like add a "Did you mean" string, Add this string to a posts search terms...','i_search'); 

/* 
 * Help info for: Suggestions order 
 */
 
$return_data['help_25']['html'] = __('The order of the search results.
<strong>Order by latest post:</strong> The search results will be ordered by the last inserted post.
<strong>Order by click popularity:</strong> i-Search will log the user clicks on the search suggestion and build a popularity index.','i_search');

/* 
 * Help info for: Database actions 
 */
 
$return_data['help_26']['html'] = __('Database actions. You can empty i-Search database tables. And start from the beginning.','i_search');

/* 
 * Help info for: Popularity index 
 */

$return_data['help_27']['html'] = __('Popularity index. i-Search will log every click on search suggestions and generate a popularity index based on clicks. The popularity index is useful if you want to show popular searches instead of a "Nothing Found" string. Popularity index is also good for search results ordering. You can select in the "Order search results" option: order by click popularity. If you disable this checkbox. Make sure that you have NOT selected the order by click popularity. If you still select the order by click popularity option. This checkbox will automatically selected on save.','i_search');
$return_data['help_27']['img'] = $img_url.'popular.jpg';
$return_data['help_27']['img_h'] = 300;

/*
 * Help info for: Popular Searches label.
 */

$return_data['help_28']['html'] = __('The label for todays popular searches. (If popular searches available and visitors search do not return any result.)','i_search');
$return_data['help_28']['img'] = $img_url.'popular_help_1.png';
$return_data['help_28']['img_h'] = 250;

/* 
 * Help info for: Mobile options 
 */
 
$return_data['help_29']['html'] = __('Hide the selected fields for Phones/Tablets.','i_search'); 

/* 
 * Help info for: jQuery include 
 */
 
$return_data['help_30']['html'] = __('Some templates have not jQuery included by default. <br>Enable this checkbox to include the jQuery library in front.','i_search'); 

/* 
 * Help info for: Enable add to cart 
 */
 
$return_data['help_31']['html'] = __('ONLY in advanced theme.','i_search'); 
$return_data['help_31']['img'] = $img_url.'add_tc.jpg'; 
$return_data['help_31']['img_h'] = 300; 

/* 
 * Help info for: Theme select 
 */
 
$return_data['help_32']['html'] = __('On mobile devices always the clean template is used. Theme screenshots:','i_search'); 
$return_data['help_32']['img'] = $img_url.'theme_anim.gif';
$return_data['help_32']['img_h'] = 400;

/*
 * Help info for: Shortcode list
 */

$return_data['help_34']['html'] = __('You can create unlimited search instances with customized settings.','i_search');

/*
 * Help info for: Custom css
 */

$return_data['help_35']['html'] = __('Add your custom CSS. Ignore the parent DIV with "isrc_sc_..." class. The parent class will added automatically to your CSS','i_search');

/*
 * Help info for: Theme select
 */

$return_data['help_36']['html'] = __('Orientation of the suggestions container. "Auto" will calculate and choose the best orientation. Example with Top orientation:','i_search');
$return_data['help_36']['img'] = $img_url.'suggestions_top.jpg';
$return_data['help_36']['img_h'] = 400;

/*
 * Help info for: Offset
 */

$return_data['help_37']['html'] = __('Offset between input and suggestions container','i_search');
$return_data['help_37']['img'] = $img_url.'custom_offset.jpg';
$return_data['help_37']['img_h'] = 400;

/*
 * Help info for: Continue scrolling
 */

$return_data['help_38']['html'] = __('Auto load more suggestions on scroll to bottom.','i_search');
$return_data['help_38']['video'] = 'https://i-search.all4wp.net/videos/continue-scroll.mp4';
$return_data['help_38']['video_w'] = 480;
$return_data['help_38']['video_h'] = 328;

/*
 * Help info for: show more
 */

$return_data['help_39']['html'] = __('Load more suggestions on click.','i_search');
$return_data['help_39']['video'] = 'https://i-search.all4wp.net/videos/show-more.mp4';
$return_data['help_39']['video_w'] = 480;
$return_data['help_39']['video_h'] = 328;

/*
 * Help info for: placeholder advertising
 */

$return_data['help_40']['html'] = __('Shows randomly selected text string in the search input placeholder. Add text strings by clicking the "Add New Row" button.','i_search');

/*
 * Help info for: Make this instance default
 */

$return_data['help_41']['html'] = __('Default instance for replacing the WP search form (if enabled in settings).','i_search');

/*
 * Help info for: Exclude words from display
 */

$return_data['help_42']['html'] = __('This words are always excluded displaying in suggestions. For example you have a category which is almost present in every post. Here you can enter the words you want to exclude. Wildcards are allowed. If you have the category "Uncategorized" almost in every post. Add it here to prevent it to being displayed in suggestions.','i_search');

/*
 * Help info for: Keyword ignore
 */

$return_data['help_43']['html'] = __('Lets assume you want to search for: "All Quiet on the Western Front". With the option keep search words order : A search for "Quiet Western Front" will return no suggestions. With the option ignore search words order: A search for "Quiet front western" will return suggestions. i-Search will look for every single word in the search string. For pluralization you need to add plural letters in the advanced settings.','i_search');

/*
 * Help info for: ip limit
 */

$return_data['help_44']['html'] = __('Max logs allowed from the same ip address. If this limit is reached all requests from the same ip address will be blocked for logging actions for 24 Hours. The user still get search suggestions but i-Search will not log his clicks or search terms. This is the number for Max log entries in the database from the same ip.','i_search');

/*
 * Help info for: exclude tags
 */

$return_data['help_45']['html'] = __('Will exclude all connected post types with the selected tag. Example: If you want to exclude some categories from the suggestions, select the post type, the taxonomy and start typing the category name. Select the category from the drop-down to exclude it.','i_search');

/*
 * Help info for: interchange
 */

$return_data['help_46']['html'] = __('Word string to replace the users search string. Will replace in booth directions. Example: Replace the and sign "&" with the word "and". Now a search for "You & Me" will return the post "You AND Me" or "You & Me". Please note that i-Search will replace whole words or strings. Example for "and" replacement: The word "androcentric" will not be replaced. All words with a whitespace + "and" + whitespace will be replaced.','i_search');

/*
 * Help info for: plurals
 */

$return_data['help_47']['html'] = __('Add plural ending letters here. These letters will be added to every single word in the search terms. Example: if you add the letter "S", a search for "The meaningS of life" will return the original post title "The meaning of life".','i_search');

/*
 * Help info for: screen position
 */

$return_data['help_48']['html'] = __('The position of the input field on the screen. Fixed position may not be clearly understandable in the preview. Enter the posisions of Top, Right, Bottom and Left. You can enter a value in pixel(px) or %. Zero does not mean empty! clear a value to disable the value. Example: Right: 100px, Left: 0 will display the input field with a margin of 100px to right and 0px distance to left (Full width). Where Right:100px and an empty value for Left will ignore the input fields width (You can set the min width in the css settings). A good starting point is: Top: 100px, Right: 30px, Bottom: leave empty, Left: 50% ','i_search');

/*
 * Help info for: screen position
 */

$return_data['help_49']['html'] = __('Text string under the search field. With trending search tags.','i_search');

$content_id = $_REQUEST['content_id'];
wp_send_json( $return_data[ $content_id ] );
