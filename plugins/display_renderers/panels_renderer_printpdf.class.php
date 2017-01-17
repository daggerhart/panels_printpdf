<?php
/**
 * @file
 * Contains the printpdf display renderer.
 */

/**
 * Display render that delivers the output of a page/panel to the user as a PDF
 * using the print_pdf module and settings.
 */
class panels_renderer_printpdf extends panels_renderer_standard {

  public $options = array();

  /**
   * Load our saved options from the page_manager handler $conf array
   *
   * @param $default_options
   */
  function set_options( $default_options ){
    $handlers = ctools_export_load_object('page_manager_handlers', 'names', array( $this->display->storage_id ) );
    $handler = reset( $handlers );

    $this->options = array_replace( $default_options, $handler->conf );
  }

  /**
   * Render the layout according to panels_renderer_standard, then process
   * the content the same way print_pdf module works, and deliver PDF to the
   * screen.
   *
   * @return void
   */
  function render_layout() {
    $content = parent::render_layout();

    $pdf_filename = $this->options['pdf_filename'];

    // filename context substitutions and transliteration
    if ( !empty( $this->display->context ) ){
      $pdf_filename = ctools_context_keyword_substitute( $pdf_filename, array(), $this->display->context );
      $pdf_filename = filter_xss_admin( $pdf_filename );
    }

    if ( function_exists('transliteration_clean_filename') ) {
      $pdf_filename = transliteration_clean_filename( $pdf_filename, language_default('language') );
    }

    $pdf = $this->generate_pdf( $content );

    if ($pdf == NULL) {
      drupal_set_message( t("Error generating PDF on @uri", array( '@uri' => $_GET['q'] ) ) );
      drupal_goto();
      exit;
    }

    print_pdf_dispose_content($pdf, $pdf_filename);
    drupal_exit();
  }

  /**
   * Taken from print_pdf.pages.inc. Simplified (no caching) for now.
   *
   * @param $content
   *
   * @return null|string
   * @throws \Exception
   */
  function generate_pdf( $content ) {
    global $base_url;
    module_load_include('inc', 'print', 'print.pages' );

    // Call the current tool's hook_pdf_tool_info(), to see if we need to expand CSS
    $pdf_tool = explode('|', variable_get('print_pdf_pdf_tool', PRINT_PDF_PDF_TOOL_DEFAULT));

    $function = $pdf_tool[0] . '_pdf_tool_info';
    if (function_exists($function)) {
      $info = $function();
    }
    $expand = isset($info['expand_css']) ? $info['expand_css'] : FALSE;
    $link = print_pdf_print_link();

    $query = $_GET;
    unset($query['q']);

    // Fake node to trick print_preprocess_print()
    $node = new stdClass();
    $node->nid = 0;
    $node->path = $_GET['q'];
    $node->title = $this->display->get_title();
    $node->content = $content;

    $html = theme('print', array(
      'node' => $node,
      'query' => $query,
      $expand,
      'format' => $link['format']
    ));

    // Img elements must be set to absolute
    $pattern = '!<(img\s[^>]*?)>!is';
    $html = preg_replace_callback($pattern, '_print_rewrite_urls', $html);

    // Convert the a href elements, to make sure no relative links remain
    $pattern = '!<(a\s[^>]*?)>!is';
    $html = preg_replace_callback($pattern, '_print_rewrite_urls', $html);
    // And make anchor links relative again, to permit in-PDF navigation
    $html = preg_replace("!${base_url}/" . $link['path'] . '/.*?#!', '#', $html);

    $meta = array(
      'node' => $node,
      'url' => url($node->path, array('absolute' => TRUE)),
    );

    $pdf = print_pdf_generate_html($html, $meta, NULL, $this->options['pdf_paper_size'], $this->options['pdf_page_orientation']);

    return !empty($pdf) ? $pdf : NULL;
  }
}
