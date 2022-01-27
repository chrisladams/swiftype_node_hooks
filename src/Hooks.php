<?php

function swiftypeCrawlUrl($nodeUrl)
{
  $active = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__active');
  $engine_id = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__engine_id');
  $domain_id = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__domain_id');
  $token = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__api_key');
  if ($active && $engine_id && $domain_id && $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.swiftype.com/api/v1/engines/'.$engine_id.'/domains/'.$domain_id.'/crawl_url.json');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
      'auth_token' => $token,
      'url' => $nodeUrl
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    $output = curl_exec($ch);
    curl_close($ch);
    \Drupal::logger('swiftype_node_hooks')->notice('Node was published and sent to Swiftype: ' . $nodeUrl ."\n" . $output);
  }
}

function swiftypeDeleteUrl($nodeUrl)
{
  $active = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__active');
  $engine_id = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__engine_id');
  $domain_id = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__domain_id');
  $token = \Drupal::config('swiftype_node_hooks.settings')->get('swiftype_node_hooks__api_key');
  if ($active && $engine_id && $domain_id && $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.swiftype.com/api/v1/engines/' . $engine_id . '/document_types/page/documents/destroy_url');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
      'auth_token' => $token,
      'url' => $nodeUrl
    ]));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    \Drupal::logger('swiftype_node_hooks')->notice('Node was unpublished and deleted from Swiftype: ' . $nodeUrl ."\n" . $output);
  }
}

function swiftype_node_hooks_node_insert(\Drupal\Core\Entity\EntityInterface $node)
{
  if ($node->get('status')[0]->value == 1) {
    $nodeUrl = $node->toUrl('canonical', ['absolute' => true])->toString();
    swiftypeCrawlUrl($nodeUrl);
  }
}

function swiftype_node_hooks_entity_update(Drupal\Core\Entity\EntityInterface $entity) {
  if ($entity->getEntityTypeId() == 'node') {
    $nodeUrl = $entity->toUrl('canonical', ['absolute' => true])->toString();
    if ($entity->get('status')[0]->value != 1 && $entity->original->get('status')[0]->value == 1) {
      swiftypeDeleteUrl($nodeUrl);
    } elseif ($entity->get('status')[0]->value == 1 && (empty($entity->original) || $entity->original->get('status')[0]->value != 1)) {
      swiftypeCrawlUrl($nodeUrl);
    }
  }
}

function swiftype_node_hooks_node_predelete(\Drupal\Core\Entity\EntityInterface $node) {
  if ($node->get('status')[0]->value == 1) {
    $nodeUrl = $node->toUrl('canonical', ['absolute' => true])->toString();
    swiftypeDeleteUrl($nodeUrl);
  }
}