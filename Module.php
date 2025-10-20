<?php

namespace ShareButton;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * ShareButton module main class (Sharing-compatible).
 */
class Module extends AbstractModule {

  /**
   * Return module configuration.
   */
  public function getConfig() {
    return include sprintf('%s/config/module.config.php', __DIR__);
  }

  /**
   * Bootstrap hook.
   *
   * @param \Laminas\Mvc\MvcEvent $event
   *   Mvc event.
   */
  public function onBootstrap(MvcEvent $event) {
    parent::onBootstrap($event);

    $acl = $this->getServiceLocator()->get('Omeka\Acl');
    $acl->allow(NULL, ['ShareButton\\Controller\\Index', 'ShareButton\\Controller\\Oembed']);
  }

  /**
   * Install hook.
   *
   * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator
   *   Service locator.
   */
  public function install(ServiceLocatorInterface $serviceLocator) {
    $messenger = $serviceLocator->get('ControllerPluginManager')->get('messenger');
    // @translate
    $messenger->addSuccess('Sharing options are site-specific. Site owners will need to set the options for their sites.');
  }

  /**
   * Attach listeners to Omeka events.
   *
   * @param \Laminas\EventManager\SharedEventManagerInterface $sharedEventManager
   *   Shared event manager.
   */
  public function attachListeners(SharedEventManagerInterface $sharedEventManager) {
    // Add site settings.
    $sharedEventManager->attach(
      'Omeka\Form\SiteSettingsForm',
      'form.add_elements',
      [$this, 'addSiteSettingsForm']
    );
    $sharedEventManager->attach(
      'Omeka\Form\SiteSettingsForm',
      'form.add_input_filters',
      [$this, 'addSiteSettingsFormFilters']
    );

    // Add sharing methods to public pages.
    $resources = [
      'Omeka\\Controller\\Site\\Item',
      'Omeka\\Controller\\Site\\Media',
      'Omeka\\Controller\\Site\\Page',
    ];
    foreach ($resources as $resource) {
      $sharedEventManager->attach(
        $resource,
        'view.show.before',
        [$this, 'addSharingMethods']
      );
      $sharedEventManager->attach(
        $resource,
        'view.show.after',
        [$this, 'addSharingMethods']
      );
      // Add Open Graph meta if not already present.
      $sharedEventManager->attach(
        $resource,
        'view.show.before',
        [$this, 'addOpenGraphHeadMeta']
      );
    }

    // Add discoverable oEmbed head links to public pages.
    foreach ($resources as $resource) {
      $sharedEventManager->attach(
        $resource,
        'view.show.after',
        [$this, 'addOembedHeadLink']
      );
    }

    // Copy Sharing-related data for the CopyResources module.
    $sharedEventManager->attach(
      '*',
      'copy_resources.sites.post',
      function (Event $event) {
        $copyResources = $event->getParam('copy_resources');
        $siteCopy = $event->getParam('resource_copy');

        $copyResources->revertSiteBlockLayouts($siteCopy->id(), 'sharing');
      }
    );
  }

  /**
   * Add site-specific settings fields for the module.
   */
  public function addSiteSettingsForm(Event $event) {
    $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');
    $form = $event->getTarget();

    $groups = $form->getOption('element_groups');
    // @translate
    $groups['sharebutton'] = 'ShareButton';
    $form->setOption('element_groups', $groups);

    $enabledMethods = $siteSettings->get('sharing_methods', []);
    $placement = $siteSettings->get('sharing_placement', '');
    $display = (int) $siteSettings->get('sharing_display_as_button', 0);

    $form->add([
      'name' => 'sharing_methods',
      'type' => 'multiCheckbox',
      'options' => [
        'element_group' => 'sharebutton',
        // @translate
        'label' => 'Sharing buttons',
        // @translate
        'info' => 'Select which sharing buttons to display.',
        'value_options' => [
          'fb' => [
            // @translate
            'label' => 'Facebook',
            'value' => 'fb',
            'selected' => in_array('fb', $enabledMethods),
          ],
          'twitter' => [
            // @translate
            'label' => 'Twitter',
            'value' => 'twitter',
            'selected' => in_array('twitter', $enabledMethods),
          ],
          'tumblr' => [
            // @translate
            'label' => 'Tumblr',
            'value' => 'tumblr',
            'selected' => in_array('tumblr', $enabledMethods),
          ],
          'pinterest' => [
            // @translate
            'label' => 'Pinterest',
            'value' => 'pinterest',
            'selected' => in_array('pinterest', $enabledMethods),
          ],
          'email' => [
            // @translate
            'label' => 'Email',
            'value' => 'email',
            'selected' => in_array('email', $enabledMethods),
          ],
          'embed' => [
            // @translate
            'label' => 'Embed codes',
            'value' => 'embed',
            'selected' => in_array('embed', $enabledMethods),
          ],
        ],
      ],
      'attributes' => [
        'required' => FALSE,
      ],
    ]);

    $form->add([
      'name' => 'sharing_placement',
      'type' => 'radio',
      'options' => [
        'element_group' => 'sharebutton',
        // @translate
        'label' => 'Sharing buttons placement',
        // @translate
        'info' => 'Select "Top" or "Bottom" to place the buttons on site pages, item show pages, and media show pages. Select "Using blocks" to place the buttons using page blocks and resource blocks',
        'value_options' => [
          // @translate
          'view.show.before' => 'Top',
          // @translate
          'view.show.after' => 'Bottom',
          // @translate
          '' => 'Using blocks',
        ],
      ],
      'attributes' => [
        'required' => FALSE,
        'value' => $placement,
      ],
    ]);

    $form->add([
      'name' => 'sharing_display_as_button',
      'type' => 'checkbox',
      'options' => [
        'element_group' => 'sharebutton',
        // @translate
        'label' => 'Single button',
        // @translate
        'info' => 'Check to display all sharing buttons as a single share button',
      ],
      'attributes' => [
        'id' => 'sharing_display_as_button',
        'required' => FALSE,
        'value' => $display,
      ],
    ]);

    // Optional: Facebook App ID (enables Share Dialog via FB.ui)
    $form->add([
      'name' => 'sharing_fb_app_id',
      'type' => 'text',
      'options' => [
        'element_group' => 'sharebutton',
        'label' => 'Facebook App ID (optional)',
        'info' => 'If set, the SDK will be initialized with this App ID, enabling Share Dialog via FB.ui as a fallback for mobile.',
      ],
      'attributes' => [
        'required' => FALSE,
        'value' => (string) $siteSettings->get('sharing_fb_app_id', ''),
      ],
    ]);

    // Optional: Hide Facebook share specifically on mobile (<= 640px)
    $form->add([
      'name' => 'sharing_fb_hide_mobile',
      'type' => 'checkbox',
      'options' => [
        'element_group' => 'sharebutton',
        'label' => 'Hide Facebook share on mobile',
        'info' => 'Temporarily hide the Facebook share button on small screens (<= 640px).',
      ],
      'attributes' => [
        'required' => FALSE,
        'value' => (int) $siteSettings->get('sharing_fb_hide_mobile', 1),
      ],
    ]);

    // Mobile Facebook button type: official widget (SDK) or JS-based button.
    $fbMobileMode = (string) $siteSettings->get('sharing_fb_mobile_mode', 'sdk');
    $form->add([
      'name' => 'sharing_fb_mobile_mode',
      'type' => 'radio',
      'options' => [
        'element_group' => 'sharebutton',
        'label' => 'Facebook button type on mobile',
        'info' => 'Choose which Facebook share button to render on small screens (<= 640px). "Official widget" uses Facebook SDK, "JS button" uses a lightweight link/JS fallback (no SDK).',
        'value_options' => [
          'sdk' => ['label' => 'Official widget (SDK)', 'value' => 'sdk'],
          'js'  => ['label' => 'JS button (no SDK)', 'value' => 'js'],
        ],
      ],
      'attributes' => [
        'required' => FALSE,
        'value' => in_array($fbMobileMode, ['sdk', 'js'], TRUE) ? $fbMobileMode : 'sdk',
      ],
    ]);
  }

  /**
   * Add input filters for site settings.
   */
  public function addSiteSettingsFormFilters(Event $event) {
    $inputFilter = $event->getParam('inputFilter');
    $inputFilter->add([
      'name' => 'sharing_methods',
      'required' => FALSE,
    ]);
    $inputFilter->add([
      'name' => 'sharing_placement',
      'required' => FALSE,
    ]);
    $inputFilter->add([
      'name' => 'sharing_fb_app_id',
      'required' => FALSE,
    ]);
    $inputFilter->add([
      'name' => 'sharing_fb_hide_mobile',
      'required' => FALSE,
    ]);
    $inputFilter->add([
      'name' => 'sharing_fb_mobile_mode',
      'required' => FALSE,
    ]);
  }

  /**
   * Render sharing buttons at configured placement.
   */
  public function addSharingMethods(Event $event) {
    $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');
    $enabledMethods = $siteSettings->get('sharing_methods', []);
    if (!count($enabledMethods)) {
      return;
    }

    $placement = $siteSettings->get('sharing_placement', 'view.show.before');
    $eventName = $event->getName();
    if ($eventName === $placement) {
      // See \\ShareButton\\View\\Helper\\ShareButton.
      $view = $event->getTarget();
      echo $view->shareButton();
    }
  }

  /**
   * Add Open Graph head meta.
   */
  public function addOpenGraphHeadMeta(Event $event) {
    $status = $this->getServiceLocator()->get('Omeka\Status');
    $view = $event->getTarget();
    $controller = $status->getRouteMatch()->getParam('controller');

    // Avoid duplicates: collect already-set og:* properties.
    $existing = [];
    try {
      $container = $view->headMeta()->getContainer();
      if ($container) {
        foreach ($container as $item) {
          if (isset($item->property) && strpos((string) $item->property, 'og:') === 0) {
            $existing[(string) $item->property] = TRUE;
          }
        }
      }
    }
    catch (\Throwable $e) {
      // Ignore.
    }

    $metaProperties = [
      'og:type' => 'website',
      'og:site_name' => $view->site->title(),
      'og:title' => $view->headTitle()->renderTitle(),
      'og:url' => $view->serverUrl(TRUE),
    ];
    switch ($controller) {
      case 'Omeka\\Controller\\Site\\Item':
      case 'Omeka\\Controller\\Site\\Media':
        $metaProperties['og:description'] = $view->resource->displayDescription();
        if ($primaryMedia = $view->resource->primaryMedia()) {
          $metaProperties['og:image'] = $primaryMedia->thumbnailUrl('large');
          $mediaType = $primaryMedia->mediaType();
          if ($mediaType === NULL) {
            break;
          }
          $mediaMainType = strstr($mediaType, '/', TRUE);
          switch ($mediaMainType) {
            case 'audio':
              $metaProperties['og:audio'] = $primaryMedia->originalUrl();
              break;

            case 'video':
              $metaProperties['og:video'] = $primaryMedia->originalUrl();
              break;
          }
        }
        break;

      case 'Omeka\\Controller\\Site\\Page':
        foreach ($view->page->blocks() as $block) {
          foreach ($block->attachments() as $attachment) {
            $item = $attachment->item();
            if ($item && ($primaryMedia = $item->primaryMedia())) {
              $metaProperties['og:image'] = $primaryMedia->thumbnailUrl('large');
              break 2;
            }
          }
        }
        break;
    }
    foreach ($metaProperties as $metaProperty => $metaContent) {
      if ($metaContent && empty($existing[$metaProperty])) {
        $view->headMeta()->appendProperty($metaProperty, $metaContent);
      }
    }
  }

  /**
   * Add oEmbed head link.
   */
  public function addOembedHeadLink(Event $event) {
    $view = $event->getTarget();

    $href = $view->url('oembed', [], ['force_canonical' => TRUE, 'query' => ['url' => $view->serverUrl(TRUE)]]);
    $view->headLink([
      'rel' => 'alternate',
      'type' => 'application/json+oembed',
      'title' => $view->headTitle()->renderTitle(),
      'href' => $href,
    ]);
  }

}
