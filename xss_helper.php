<?php

/**
 * Escapes data
 *
 * This is just a handy shortcut all escaping functions
 *
* @since 2.8.0
* @param string $data to be escaped
* @param string $type type of escaping needed
* @return string The cleaned $data
 */
function s( $data, $type = false){

	switch( $type ){
		case 'url':
			// remove quotes but DONT html entitity the slashes etc
			break;
        case 'sql':
            // SQL escape
            break;
        case 'html':
            // for inside javascript, remove Single quotes
            break;


		default:
			return htmlEntities($data, ENT_QUOTES);  //this will escape single quotes too
	}
}



/**
 * Escapes data for use in a MySQL query
 *
 * This is just a handy shortcut for $wpdb->escape(), for completeness' sake
 *
* @since 2.8.0
* @param string $sql Unescaped SQL data
* @return string The cleaned $sql
 */
function esc_sql( $sql ) {
        global $wpdb;
        return $wpdb->escape( $sql );
}
/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The 'clean_url' filter
 * is applied to the returned cleaned URL.
 *
* @since 2.8.0
* @uses wp_kses_bad_protocol() To only permit protocols in the URL set
 *              via $protocols or the common ones set in the function.
 *
* @param string $url The URL to be cleaned.
* @param array $protocols Optional. An array of acceptable protocols.
 *              Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn' if not set.
* @param string $_context Private. Use esc_url_raw() for database usage.
* @return string The cleaned $url after the 'clean_url' filter is applied.
 */
function esc_url( $url, $protocols = null, $_context = 'display' ) {
        $original_url = $url;
        if ( '' == $url )
                return $url;
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = _deep_replace($strip, $url);
        $url = str_replace(';//', '://', $url);
        /* If the URL doesn't appear to contain a scheme, we
         * presume it needs http:// appended (unless a relative
         * link starting with /, # or ? or a php file).
         */
        if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
                ! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
                $url = 'http://' . $url;
        // Replace ampersands and single quotes only when displaying.
        if ( 'display' == $_context ) {
                $url = wp_kses_normalize_entities( $url );
                $url = str_replace( '&amp;', '&#038;', $url );
                $url = str_replace( "'", '&#039;', $url );
        }
        if ( ! is_array( $protocols ) )
                $protocols = wp_allowed_protocols();
        if ( wp_kses_bad_protocol( $url, $protocols ) != $url )
                return '';
        return apply_filters('clean_url', $url, $original_url, $_context);
}
/**
 * Performs esc_url() for database usage.
 *
* @since 2.8.0
* @uses esc_url()
 *
* @param string $url The URL to be cleaned.
* @param array $protocols An array of acceptable protocols.
* @return string The cleaned URL.
 */
function esc_url_raw( $url, $protocols = null ) {
        return esc_url( $url, $protocols, 'db' );
}
/**
 * Convert entities, while preserving already-encoded entities.
 *
* @link http://www.php.net/htmlentities Borrowed from the PHP Manual user notes.
 *
* @since 1.2.2
 *
* @param string $myHTML The text to be converted.
* @return string Converted text.
 */
function htmlentities2($myHTML) {
        $translation_table = get_html_translation_table( HTML_ENTITIES, ENT_QUOTES );
        $translation_table[chr(38)] = '&';
        return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($myHTML, $translation_table) );
}
/**
 * Escape single quotes, htmlspecialchar " < > &, and fix line endings.
 *
 * Escapes text strings for echoing in JS. It is intended to be used for inline JS
 * (in a tag attribute, for example onclick="..."). Note that the strings have to
 * be in single quotes. The filter 'js_escape' is also applied here.
 *
* @since 2.8.0
 *
* @param string $text The text to be escaped.
* @return string Escaped text.
 */
function esc_js( $text ) {
        $safe_text = wp_check_invalid_utf8( $text );
        $safe_text = _wp_specialchars( $safe_text, ENT_COMPAT );
        $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );
        $safe_text = str_replace( "\r", '', $safe_text );
        $safe_text = str_replace( "\n", '\\n', addslashes( $safe_text ) );
        return apply_filters( 'js_escape', $safe_text, $text );
}
/**
 * Escaping for HTML blocks.
 *
* @since 2.8.0
 *
* @param string $text
* @return string
 */
function esc_html( $text ) {
        $safe_text = wp_check_invalid_utf8( $text );
        $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
        return apply_filters( 'esc_html', $safe_text, $text );
}
/**
 * Escaping for HTML attributes.
 *
* @since 2.8.0
 *
* @param string $text
* @return string
 */
function esc_attr( $text ) {
        $safe_text = wp_check_invalid_utf8( $text );
        $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
        return apply_filters( 'attribute_escape', $safe_text, $text );
}
/**
 * Escaping for textarea values.
 *
* @since 3.1
 *
* @param string $text
* @return string
 */
function esc_textarea( $text ) {
        $safe_text = htmlspecialchars( $text, ENT_QUOTES );
        return apply_filters( 'esc_textarea', $safe_text, $text );
}
/**
 * Escape a HTML tag name.
 *
* @since 2.5.0
 *
* @param string $tag_name
* @return string
 */
function tag_escape($tag_name) {
        $safe_tag = strtolower( preg_replace('/[^a-zA-Z0-9_:]/', '', $tag_name) );
        return apply_filters('tag_escape', $safe_tag, $tag_name);
}
/**
 * Escapes text for SQL LIKE special characters % and _.
 *
* @since 2.5.0
 *
* @param string $text The text to be escaped.
* @return string text, safe for inclusion in LIKE query.
 */
function like_escape($text) {
        return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
}