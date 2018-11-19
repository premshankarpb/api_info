<?php

namespace Drupal\info_api\Controller;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for json page.
 */
class JsonController extends ControllerBase {
  /**
   * Returns page details into Json format.
   */
  public function JsonapiPage() {

    $node = \Drupal::service('current_route_match');
    $node_id = $node->getParameter('node');
    $node_details = Node::load($node_id);

    $apikey_data = $node->getParameter('apikey');
    $api_key = \Drupal::config('info_api.settings')->get('siteapikey');


    if(($apikey_data != $api_key) || (empty($node_details))||($node_details->bundle() != 'page')) {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    } else {
      $type = $node_details->bundle();
      $nid = $node_details->id();
      $node_title = $node_details->title->value;
      $node_body = $node_details->body->view('full');
      $api_data = array(
        '#siteapikey' => $api_key,
        '#id' => $nid,
        '#title' => $node_title,
        '#bundle' => $type,
        '#bodycontent' => $node_body,
      );
      return new JsonResponse($api_data);
    }
  }

}
