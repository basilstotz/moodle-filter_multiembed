<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Multi-Embed filter logic
 *
 * @package    filter_multiembed
 * @copyright  2016-2017 Frederic Nevers, www.iteachwithmoodle.com
 * @author     Frederic Nevers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Multi-Embed filter definition
 *
 * Class filter_multiembed
 *
 * @copyright  2016 Frederic Nevers, www.iteachwithmoodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_multiembed extends moodle_text_filter {

    /**
     * Define the filter and run regex for each service
     * TODO: can the function be split up into smaller chunks?
     *
     * @param  string $text contained in page
     * @param  array $options
     * @return string $newtext filtered text
     */
    public function filter($text, array $options = array()) {

        if (!is_string($text) or empty($text)) {
            // Non-string data can not be filtered anyway.
            return $text;
        }

        if (stripos($text, '</a>') === false) {
            // Performance shortcut - if there is no </a> tag, nothing can match.
            return $text;
        }

        // Return the original text if the regex fails.
        $newtext = $text;

        // Search for ClassTools tools.
        if (get_config('filter_multiembed', 'classtools')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(classtools\.net)\/(movietext|SMS|brainybox|';
            $search .= 'qwikslides|telescopic|arcade|hexagon|random-name-picker)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_classtoolscallback', $newtext);
        }

        // Search for CodePen snippets.
        if (get_config('filter_multiembed', 'codepen')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(codepen\.io)\/(.*?)\/(pen)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_codepencallback', $newtext);
        }

        // Search for Desmos calculators.
        if (get_config('filter_multiembed', 'desmos')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(desmos\.com)\/(calculator)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_desmoscallback', $newtext);
        }

        // Search for DiagnosticQuestions Questions and Quizzes.
        if (get_config('filter_multiembed', 'diagnosticq')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(diagnosticquestions\.com)';
            $search .= '\/(Questions|Quizzes)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_diagnosticqcallback', $newtext);
        }

        // Search for eMaze.
        if (get_config('filter_multiembed', 'emaze')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(emaze\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_emazecallback', $newtext);
        }

        // Search for Etherpad.
        if (get_config('filter_multiembed', 'etherpad')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(etherpad\.)openstack\.org)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_etherpadcallback', $newtext);
        }

        // Search for Google Docs, Drawings, Forms, Presentations and Sheets.
        if (get_config('filter_multiembed', 'gdocs')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(docs\.))(google\.com)\/(document|drawings|forms|';
            $search .= 'presentation|spreadsheets)\/(.*?)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_gdocscallback', $newtext);
        }

        // Search for GSuite shared documents.
        if (get_config('filter_multiembed', 'gsuite')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(docs\.))(google\.com)\/(.*?)\/(.*?)';
            $search .= '\/(.*?)\/(.*?)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_gsuitecallback', $newtext);
        }

        // Search for Haiku decks.
        if (get_config('filter_multiembed', 'haiku')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(haikudeck\.com)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_haikucallback', $newtext);
        }

        // Search for Imgur images.
        if (get_config('filter_multiembed', 'imgur')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(imgur\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_imgurcallback', $newtext);
        }

        // Search for Infogr.am visualisations.
        if (get_config('filter_multiembed', 'infogram')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(infogr\.am)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_infogramcallback', $newtext);
        }

        // Search for Padlet boards.
        if (get_config('filter_multiembed', 'padlet')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(padlet\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_padletcallback', $newtext);
        }

        // Search for PBS videos.
        if (get_config('filter_multiembed', 'pbs')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(pbs\.org)\/(video)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_pbscallback', $newtext);
        }

        // Search for Piktochart visualisations.
        if (get_config('filter_multiembed', 'piktochart')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(magic\.)?)(piktochart\.com)\/(output)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_piktochartcallback', $newtext);
        }

        // Search for PollEverywhere polls.
        if (get_config('filter_multiembed', 'pollev')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(polleverywhere\.com|PollEv.com)\/(discourses|';
            $search .= 'polls|multiple_choice_polls|free_text_polls|ranking_polls)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_pollevcallback', $newtext);
        }

        // Search for Prezi presentations.
        if (get_config('filter_multiembed', 'prezi')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(prezi\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_prezicallback', $newtext);
        }

        // Search for Quizlet actvities.
        if (get_config('filter_multiembed', 'quizlet')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(quizlet\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_quizletcallback', $newtext);
        }

        // Search for Riddle quizzes.
        if (get_config('filter_multiembed', 'riddle')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(riddle\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_riddlecallback', $newtext);
        }

        // Search for Slid.es presentations.
        if (get_config('filter_multiembed', 'slides')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(slides\.com)\/(.*?)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_slidescallback', $newtext);
        }

        // Search for Smore creations.
        if (get_config('filter_multiembed', 'smore')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(smore\.com)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_smorecallback', $newtext);
        }

        // Search for Soundcloud tracks.
        if (get_config('filter_multiembed', 'soundcloud')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(soundcloud\.com)\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_soundcloudcallback', $newtext);
        }

        // Search for Studystack items.
        if (get_config('filter_multiembed', 'studystack')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(studystack\.com)\/(.*?)\-(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_studystackcallback', $newtext);
        }

        // Search for Sutori timelines.
        if (get_config('filter_multiembed', 'sutori')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(sutori\.com)\/timeline\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_sutoricallback', $newtext);
        }

        // Search for TED Videos.
        if (get_config('filter_multiembed', 'ted')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(ted\.com)\/talks\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_tedcallback', $newtext);
        }

        // Search for Thinglink images.
        if (get_config('filter_multiembed', 'thinglink')) {
            $search = '/<a\s[^>]*href="(https?:\/\/(www\.)?)(thinglink\.com)\/scene\/(.*?)"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_thinglinkcallback', $newtext);
        }

        if (get_config('filter_multiembed', 'youtube')) {
            $search = '/<a\s[^>]*href="((https?:\/\/(www\.)?)(youtube\.com|youtu\.be|youtube\.googleapis.com)';
            $search .= '\/(?:embed\/|v\/|watch\?v=|watch\?.+?&amp;v=|watch\?.+?&v=)?((\w|-){11})(.*?))"(.*?)>(.*?)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_multiembed_youtubecallback', $newtext);
        }

        // Exception checks.
        if (empty($newtext) or $newtext === $text) {
            // Error or not filtered.
            unset($newtext);
            return $text;
        }

        return $newtext;
    }

}

/**
 * Turns a ClassTools link into an embedded item
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_classtoolscallback($link) {

    $itemtype = $link[4]; // ClassTools item type is in 4th group of regex.

    switch ($itemtype) {
        case 'arcade':
            $embedcode = '<p align="center"><iframe scrolling="no" src="//www.classtools.net/widg/';
            $embedcode .= $link[5];
            $embedcode .= '" width="570" height="250" frameborder=0></iframe></p>';
            break;
        case 'brainybox':
            $embedcode = '<p align="center"><iframe scrolling="no" src="//www.classtools.net/brainybox/';
            $embedcode .= $link[5];
            $embedcode .= '&widget=1" width="650" height="650" frameborder=0></iframe></p>';
            break;
        case 'hexagon':
            $embedcode = '<iframe src="//www.classtools.net/hexagon/';
            $embedcode .= $link[5];
            $embedcode .= '" style="background-color:white;position:relative;top:0;left:0;width:750px';
            $embedcode .= ';height:300px;overflow:none;border:none"></iframe>';
            break;
        case 'movietext':
            $embedcode = '<iframe src="//www.classtools.net/movietext/';
            $embedcode .= $link[5];
            $embedcode .= '" style="position:relative;top:0;left:0;width:900px;height:650px';
            $embedcode .= ';overflow:none;border:none"></iframe>';
            break;
        case 'qwikslides':
            $embedcode = '<iframe src="//www.classtools.net/qwikslides/';
            $embedcode .= $link[5];
            $embedcode .= '" style="position:relative;top:0;left:0;width:675px;height:600px';
            $embedcode .= ';overflow:none;border:none"></iframe>';
            break;
        case 'random-name-picker':
            $embedcode = '<iframe src="//www.classtools.net/random-name-picker/';
            $embedcode .= $link[5];
            $embedcode .= '" style="position:relative;top:0;left:0;width:675px';
            $embedcode .= ';height:600px;overflow:none;border:none"></iframe>';
            break;
        case 'SMS':
            $embedcode = '<iframe src="//www.classtools.net/SMS/';
            $embedcode .= $link[5];
            $embedcode .= '" style="position:relative;top:0;left:0;width:267px';
            $embedcode .= ';height:460px;overflow:none;border:none"></iframe>';
            break;
        case 'telescopic':
            $embedcode = '<div id="telescopic" style="position:relative;padding-bottom:45%;height:0;">';
            $embedcode .= '<iframe id="fr" src="//www.classtools.net/telescopic/';
            $embedcode .= $link[5];
            $embedcode .= '?embedded=1" style="position:absolute;top:0;left:0;width:100%';
            $embedcode .= ';height:90%;overflow:hidden;border:none"></iframe></div>';
            break;
        default:
            $embedcode = 'issue';
    }

    return $embedcode;
}

/**
 * Turns a CodePen link into an embedded snippet
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_codepencallback($link) {
    $embedcode = '<iframe height="265" scrolling="no" src="//codepen.io/';
    $embedcode .= $link[4]; // CodePen user ID is in 4th capturing group of the regex.
    $embedcode .= '/embed/';
    $embedcode .= $link[6]; // CodePen snippet ID is in 6th capturing group of the regex.
    $embedcode .= '/?height=265&amp;theme-id=0&amp;default-tab=css,result&embed-version=2" frameborder="no"';
    $embedcode .= ' allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>';

    return $embedcode;
}

/**
 * Turns a Desmos link into an embedded graphic calculator
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_desmoscallback($link) {
    $embedcode = '<a title="View with the Desmos Graphing Calculator" href="https://www.desmos.com/calculator/';
    $embedcode .= $link[5]; // Desmos graphing calculators are in 5th capturing group of the regex.
    $embedcode .= '">  <img src="https://s3.amazonaws.com/calc_thumbs/production/';
    $embedcode .= $link[5].'.png'; // Desmos graphing calculators are in 5th capturing group of the regex.
    $embedcode .= '" width="200px" height="200px" style="border:1px solid #ccc; border-radius:5px"/></a>';

    return $embedcode;
}

/**
 * Turns a Diagnostic Questions link into an embedded question or quizz
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_diagnosticqcallback($link) {
    $embedcode = '<iframe width="664" height="568" src="https://diagnosticquestions.com/';
    $embedcode .= $link[4]; // Diagnostic Questions item types are in 4th capturing group of the regex.
    $embedcode .= '/Embed#/';
    $embedcode .= $link[6]; // Diagnostic Questions IDs are in 4th capturing group of the regex.
    $embedcode .= '" frameborder="0"></iframe>';

    return $embedcode;
}

/**
 * Turns an emaze link into an embedded presentation
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_emazecallback($link) {
    $embedcode = '<iframe src="//app.emaze.com/';
    $embedcode .= $link[4].'/'.$link[5]; // Emaze presentation IDs are in the 4th capturing group of the regex.
    $embedcode .= '" width="960px" height="540px" seamless webkitallowfullscreen';
    $embedcode .= ' mozallowfullscreen allowfullscreen></iframe>';

    return $embedcode;
}

/**
 * Turns an Etherpad link into an embedded document
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_etherpadcallback($link) {
    $embedcode = '<iframe name="embed_readwrite" src="//etherpad.openstack.org/p/';
    $embedcode .= $link[4]; // Etherpad document IDs are in the 4th capturing group of the regex.
    $embedcode .= '?showControls=true&showChat=true&showLineNumbers=true&useMonospaceFont=false" width=600 height=400></iframe>';

    return $embedcode;
}

/**
 * Turns a Google Doc, Drawing, Form, Sheet or Slides link into an embedded (editable) document
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_gdocscallback($link) {
    $embedcode = '<iframe height="620" width="100%" border="0" src="//docs.google.com/';
    $embedcode .= $link[4].'/'; // Service type is in 4th capturing group of regex.
    $embedcode .= $link[5].'/'; // Unsure letter is always the same.
    $embedcode .= $link[6].'/'; // Google Doc IDs are in the 6th capturing group of the regex.

    // Google forms follow a slightly different logic.
    if ($link[4] != 'forms') {
        $embedcode .= 'edit?usp=sharing"></iframe>';
    } else {
        $embedcode .= '"></iframe>';
    }

    return $embedcode;
}

/**
 * Turns a GSuite owned and shared Google Doc, Drawing, Form, Sheet or Slides link into an embedded (editable) document
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_gsuitecallback($link) {
    $embedcode = '<iframe height="620" width="100%" border="0" src="//docs.google.com/';
    $embedcode .= $link[6].'/'; // Service type is in 6th capturing group of regex.
    $embedcode .= $link[7].'/'; // Unsure letter is always the same (should be a 'd').

    if ($link[8] == 'e') {     // If the 8th capturing group is the letter 'e';
        $embedcode .= $link[8].'/'.strtok($link[9], '/').'/'; // Then the Google Doc IDs are in the 9th group of the regex.
    } else {
        $embedcode .= strtok($link[8], '/').'/'; // Otherwise the IDs are in the 8th capturing group of the regex.
    }

    // Google forms follow a slightly different logic.
    if ($link[6] != 'forms') {
        $embedcode .= 'edit?usp=sharing"></iframe>';
    } else {
        $embedcode .= 'viewform"></iframe>';
    }

    return $embedcode;
}

/**
 * Turns a Haiku link into an embedded presentation
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_haikucallback($link) {
    $embedcode = '<iframe src="//www.haikudeck.com/e/';
    // Only keep the last 10 characters of any URL, those are the deck IDs.
    $embedcode .= substr($link[4], -10); // Haiku deck IDs are in the 4th capturing group of the regex.
    $embedcode .= '/?isUrlHashEnabled=false&isPreviewEnabled=false&isHeaderVisible=false"';
    $embedcode .= 'width="640" height="541" frameborder="0" marginheight="0" marginwidth="0"></iframe>';

    return $embedcode;
}

/**
 * Turns an Imgur link into an embedded image
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_imgurcallback($link) {
    $embedcode = '<blockquote class="imgur-embed-pub" lang="en" data-id="a/';
    $embedcode .= $link[5]; // Imgur images are in the 5th capturing group of the regex.
    $embedcode .= '"><a href="//imgur.com/';
    $embedcode .= $link[5];
    $embedcode .= '">';
    $embedcode .= $link[5];
    $embedcode .= '</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>';

    return $embedcode;
}

/**
 * Turns an Infogr.am link into an embedded visualisation
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_infogramcallback($link) {
    $embedcode = '<iframe src="//e.infogr.am/';
    $embedcode .= $link[4]; // Infogr.am visualisation IDs are in the 5th capturing group of the regex.
    $embedcode .= '?src=embed" title="Top Earners" width="700" height="580"';
    $embedcode .= 'scrolling="no" frameborder="0" style="border:none;"></iframe>';

    return $embedcode;
}

/**
 * Turns a Padlet link into an embedded video
 * iframe code from www.padlet.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_padletcallback($link) {
    $embedcode = '<iframe src="//padlet.com/embed/';
    $embedcode .= $link[5]; // Padlet IDs are in the 4th capturing group of the regex.
    $embedcode .= '" frameborder="0" width="100%" height="480px" style="padding:0;margin:0;border:none"></iframe>';

    return $embedcode;
}

/**
 * Turns a PBS link into an embedded video
 * iframe code from www.pbs.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_pbscallback($link) {
    $embedcode = '<iframe width="512" height="376" src="//player.pbs.org/viralplayer/';
    $embedcode .= $link[5]; // PBS IDs are in the 4th capturing group of the regex.
    $embedcode .= '" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" seamless allowfullscreen></iframe>';

    return $embedcode;
}

/**
 * Turns a Piktochart link into an embedded poll
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_piktochartcallback($link) {
    $embedcode = '<div class="piktowrapper-embed" pikto-uid="';
    $embedcode .= $link[5]; // Piktochart ID is in 5th capturing group of regular expression.
    $embedcode .= '" style="height: 300px; position: relative;"><div class="embed-loading-overlay" style="width: 100%;';
    $embedcode .= ' height: 100%; position: absolute; text-align: center;">';
    $embedcode .= '<img width="60px" alt="Loading..." style="margin-top: 100px" ';
    $embedcode .= 'src="//magic.piktochart.com/loading.gif"/>';
    $embedcode .= '<p style="margin: 0; padding: 0; font-family: Lato, Helvetica, Arial, sans-serif;';
    $embedcode .= 'font-weight: 600; font-size: 16px">Loading...</p></div><div class="pikto-canvas-wrap">';
    $embedcode .= '<div class="pikto-canvas"></div></div></div>';
    $embedcode .= '<script>(function(d){var js, id="pikto-embed-js", ref=d.getElementsByTagName("script")[0]';
    $embedcode .= ';if (d.getElementById(id)) { return;}js=d.createElement("script")';
    $embedcode .= ';js.id=id; js.async=true;js.src="//magic.piktochart.com/assets/embedding/embed.js"';
    $embedcode .= ';ref.parentNode.insertBefore(js, ref);}(document));</script>';

    return $embedcode;
}


/**
 * Turns a PollEverywhere link into an embedded poll
 * iframe code from www.polleverywhere.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_pollevcallback($link) {
    $embedcode = '<iframe src="//embed.polleverywhere.com/';
    $embedcode .= $link[4].'/'; // Type of activity.
    // Strip any unwanted parts of ID.
    $embedcode .= strtok($link[5], "/"); // PollEv IDs are in the 5th capturing group of the regex.
    $embedcode .= '?controls=none&amp;short_poll=true" width="100%" height="100%" frameBorder="0"></iframe>';

    return $embedcode;
}

/**
 * Turns a Prezi link into an embedded presentation
 * iframe code from www.prezi.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_prezicallback($link) {
    $embedcode = '<iframe id="iframe_container" frameborder="0" webkitallowfullscreen="" mozallowfullscreen=""';
    $embedcode .= 'allowfullscreen="" width="550" height="400" src="//prezi.com/embed/';
    $embedcode .= $link[4]; // Prezi IDs are in the 4th capturing group of the regex.
    $embedcode .= '/?bgcolor=ffffff&amp;lock_to_path=0&amp;autoplay=0&amp;autohide_ctrls=0"></iframe>';

    return $embedcode;
}

/**
 * Turns a Quizlet link into an embedded activity
 * iframe code from www.quizlet.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_quizletcallback($link) {
    $embedcode = '<iframe src="//quizlet.com/';
    $embedcode .= $link[4]; // Quizlet IDs are in the 4th capturing group of the regex.
    $embedcode .= '/flashcards/embed" height="410" width="100%" style="border:0"></iframe>';

    return $embedcode;
}

/**
 * Turns a Riddle link into an embedded quizz
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_riddlecallback($link) {
    $embedcode = '<div class="riddle_target" data-rid-id="';
    $embedcode .= basename($link[5]); // Riddle IDs are in the 5th capturing group of the regex.
    $embedcode .= '" data-fg="#1486cd" data-bg="#FFFFFF" style="margin:0 auto;max-width:640px">';
    $embedcode .= '<script src="https://www.riddle.com/files/js/embed.js"></script>';
    $embedcode .= '<iframe style="width:100%;height:300px;border:1px solid #cfcfcf" src="//riddle.com/a/';
    $embedcode .= basename($link[5]);
    $embedcode .= '?"></iframe></div>';

    return $embedcode;
}

/**
 * Turns a Slid.es link into an embedded presentation
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_slidescallback($link) {
    $embedcode = '<iframe src="//slides.com/';
    $embedcode .= $link[4].'/'; // Slid.es user IDs are in the 5th capturing group of the regex.
    $embedcode .= strtok($link[5], "/"); // Slid.es slide IDs are in the 6th capturing group of the regex.
    $embedcode .= '/embed" width="576" height="420" scrolling="no" frameborder="0"';
    $embedcode .= 'webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

    return $embedcode;
}

/**
 * Turns a Smore link into an embedded creation
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_smorecallback($link) {
    $embedcode = '<iframe width="100%" height="600" src="//www.smore.com/';
    $embedcode .= $link[4]; // Smore IDs are in the 4th capturing group of the regex.
    $embedcode .= '?embed=1" scrolling="auto" frameborder="0" allowtransparency="true"';
    $embedcode .= ' style="min-width: 320px;border: none;"></iframe>';

    return $embedcode;
}

/**
 * Turns a Soundcloud link into an embedded song
 * Please note that this may not work forever,
 * As it is a workaround (soundcloud API normally asks for tracks IDs)
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_soundcloudcallback($link) {
    $embedcode = '<iframe width="100%" height="166" scrolling="no" frameborder="no"';
    $embedcode .= ' src="//w.soundcloud.com/player/?url=https%3A//soundcloud.com/';
    $embedcode .= $link[4]; // Soundcloud tracks are in the 4th capturing group of the regex.
    $embedcode .= '&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;';
    $embedcode .= 'show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>';

    return $embedcode;
}

/**
 * Turns a Studystack link into an embedded item
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_studystackcallback($link) {

    $itemtype = $link[4]; // Studystack item type is in 4th group of regex.

    switch ($itemtype) {
        case 'bugmatch':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'ibugmatch';
            $scrolling = 'no';
            $width = '800';
            $height = '500';
            break;
        case 'choppedupwords':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'ichoppedupwords';
            $scrolling = 'no';
            $width = '900';
            $height = '540';
            break;
        case 'crossword':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'icrossword';
            $scrolling = 'yes';
            $width = '950';
            $height = '600';
            break;
        case 'fillin':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'ifillin';
            $scrolling = 'yes';
            $width = '747';
            $height = '1000';
            break;
        case 'flashcard':
            $iframeclass = 'studyStackFlashcardIframe';
            $urlstem = 'inewflashcard';
            $scrolling = 'no';
            $width = '850';
            $height = '440';
            break;
        case 'hangman':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'ihangman';
            $scrolling = 'no';
            $width = '520';
            $height = '540';
            break;
        case 'hungrybug':
            $iframeclass = 'studyStackFlashcardIframe';
            $urlstem = 'ihungrybug';
            $scrolling = 'no';
            $width = '890';
            $height = '600';
            break;
        case 'picmatch':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'ipicmatch';
            $scrolling = 'no';
            $width = '1038';
            $height = '660';
            break;
        case 'quiz':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'iquiz';
            $scrolling = 'yes';
            $width = '727';
            $height = '800';
            break;
        case 'studyslide':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'istudyslide';
            $scrolling = 'yes';
            $width = '1040';
            $height = '800';
            break;
        case 'studystack':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'istudystack';
            $scrolling = 'yes';
            $width = '603';
            $height = '380';
            break;
        case 'studytable':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'istudytable';
            $scrolling = 'yes';
            $width = '747';
            $height = '1000';
            break;
        case 'test':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'itest';
            $scrolling = 'yes';
            $width = '727';
            $height = '800';
            break;
        case 'wordscramble':
            $iframeclass = 'studyStackMatchingIframe';
            $urlstem = 'iwordscramble';
            $scrolling = 'yes';
            $width = '720';
            $height = '480';
            break;
        default:
            $iframeclass = 'issue';
            $urlstem = 'issue';
            $scrolling = 'no';
            $width = '850';
            $height = '440';
    }

    $embedcode = '<iframe class="';
    $embedcode .= $iframeclass.'" src="https://www.studystack.com/';
    $embedcode .= $urlstem.'-'.$link[5].'" '; // Studystack IDs are in the 5th capturing group of the regex.
    $embedcode .= 'width="'.$width.'" height="'.$height;
    $embedcode .= '" frameborder="0" scrolling="'.$scrolling;
    $embedcode .= '" style="overflow:hidden"></iframe><br /><a href="http://www.studystack.com">';
    $embedcode .= 'Flashcards and educational games by StudyStack</a><br />';

    return $embedcode;
}

/**
 * Turns a Sutori link into an embedded timeline
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_sutoricallback($link) {
    $embedcode = '<script src="https://d1ox703z8b11rg.cloudfront.net/assets/iframeResizer.js"></script>';
    $embedcode .= '<iframe src="//www.sutori.com/timeline/';
    $embedcode .= $link[4]; // Sutori timelines IDs are in the 4th capturing group of the regex.
    $embedcode .= '/embed" width="100%" scrolling="no" frameborder="0" allowfullscreen></iframe>';
    $embedcode .= '<script src="https://d1ox703z8b11rg.cloudfront.net/assets/iframeResizer.executer.js"></script>';

    return $embedcode;
}

/**
 * Turns a TED link into an embedded video
 * iframe code from www.ted.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_tedcallback($link) {
    $embedcode = '<iframe src="//embed.ted.com/talks/';
    $embedcode .= $link[4]; // TED video IDs are in the 4th capturing group of the regex.
    $embedcode .= '" width="640" height="360" frameborder="0" scrolling="no" ';
    $embedcode .= 'webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

    return $embedcode;
}

/**
 * Turns a Thinglink link into an embedded image
 * iframe code from www.thinglink.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_thinglinkcallback($link) {
    $embedcode = '<iframe width="800" height="500" src="//www.thinglink.com/card/';
    $embedcode .= $link[4]; // Thinglink image IDs are in the 4th capturing group of the regex.
    $embedcode .= '" type="text/html" frameborder="0" webkitallowfullscreen mozallowfullscreen';
    $embedcode .= ' allowfullscreen scrolling="no"></iframe>';

    return $embedcode;
}


/**
 * Turns a YouTube link into an embedded video
 * iframe code from www.youtube.com website
 *
 * @param  string $link HTML tag containing a link
 * @return string HTML content after processing.
 */
function filter_multiembed_youtubecallback($link) {
    $embedcode = '<iframe width="560" height="315" src="//www.youtube.com/embed/';
    $embedcode .= $link[5]; // YouTube video IDs are in the 5th capturing group of the regex.
    $embedcode .= '" frameborder="0" allowfullscreen></iframe>';

    return $embedcode;
}
