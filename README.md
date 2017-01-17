# Panels PrintPDF

A Drupal 7 module that allows any panel variant to render as a PDF to the browser. It adds a new "Renderer" option to panel pages named "Print PDF". This display renderer (sometimes called a renderer pipeline) delivers the rendered panel to the browser as a PDF using the print_pdf module. Enable this Renderer on a panel page variant's "General" tab.

Requires the completed install of the print_pdf module that comes with [print](https://www.drupal.org/project/print), and its PDF generation tool. Panel PDFs use the global settings as determined by the print_pdf module.

### Screenshots

![Panel with Print PDF renderer selected](http://public.daggerhart.com/images/panels-printpdf-2.png "Print PDF renderer")


### Example Panel Export

This export creates a new url that will generate PDFs for a individual nodes at `/node/%node/pdf`.

```php
$page = new stdClass();
$page->disabled = FALSE; /* Edit this to true to make a default page disabled initially */
$page->api_version = 1;
$page->name = 'nodepdf';
$page->task = 'page';
$page->admin_title = 'Node PDF';
$page->admin_description = '';
$page->path = 'node/%node/pdf';
$page->access = array(
  'logic' => 'and',
);
$page->menu = array();
$page->arguments = array(
  'node' => array(
    'id' => 1,
    'identifier' => 'Node: ID',
    'name' => 'entity_id:node',
    'settings' => array(),
  ),
);
$page->conf = array(
  'admin_paths' => FALSE,
);
$page->default_handlers = array();
$handler = new stdClass();
$handler->disabled = FALSE; /* Edit this to true to make a default handler disabled initially */
$handler->api_version = 1;
$handler->name = 'page_nodepdf__panel';
$handler->task = 'page';
$handler->subtask = 'nodepdf';
$handler->handler = 'panel_context';
$handler->weight = 0;
$handler->conf = array(
  'title' => 'Node PDF',
  'no_blocks' => 0,
  'pipeline' => 'printpdf',
  'body_classes_to_remove' => '',
  'body_classes_to_add' => '',
  'css_id' => '',
  'css' => '',
  'contexts' => array(),
  'relationships' => array(),
  'name' => 'panel',
  'access' => array(
    'logic' => 'and',
  ),
);
$display = new panels_display();
$display->layout = 'onecol';
$display->layout_settings = array();
$display->panel_settings = array(
  'style_settings' => array(
    'default' => NULL,
    'middle' => NULL,
  ),
);
$display->cache = array();
$display->title = '%node:title';
$display->uuid = '84d1d53a-dca5-45a4-ab66-90403b66602d';
$display->storage_type = 'page_manager';
$display->storage_id = 'page_nodepdf__panel';
$display->content = array();
$display->panels = array();
$pane = new stdClass();
$pane->pid = 'new-84b2513f-2921-4cf2-86cc-0add2dda57fb';
$pane->panel = 'middle';
$pane->type = 'node_content';
$pane->subtype = 'node_content';
$pane->shown = TRUE;
$pane->access = array();
$pane->configuration = array(
  'links' => 1,
  'no_extras' => 0,
  'override_title' => 0,
  'override_title_text' => '',
  'identifier' => '',
  'link' => 0,
  'leave_node_title' => 0,
  'build_mode' => 'full',
  'context' => 'argument_entity_id:node_1',
  'override_title_heading' => 'h2',
);
$pane->cache = array();
$pane->style = array(
  'settings' => NULL,
);
$pane->css = array();
$pane->extras = array();
$pane->position = 0;
$pane->locks = array();
$pane->uuid = '84b2513f-2921-4cf2-86cc-0add2dda57fb';
$display->content['new-84b2513f-2921-4cf2-86cc-0add2dda57fb'] = $pane;
$display->panels['middle'][0] = 'new-84b2513f-2921-4cf2-86cc-0add2dda57fb';
$display->hide_title = PANELS_TITLE_FIXED;
$display->title_pane = '0';
$handler->conf['display'] = $display;
$page->default_handlers[$handler->name] = $handler;
```
