<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Admin;

use Austral\WebsiteBundle\Entity\Interfaces\TrackingInterface;

use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\FormBundle\Field as Field;
use Austral\ListBundle\Column as Column;

use Exception;

/**
 * Tracking Admin.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class TrackingAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @return array
   */
  public function getEvents() : array
  {
    return array(
      FormAdminEvent::EVENT_UPDATE_BEFORE =>  "formUpdateBefore"
    );
  }

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $listAdminEvent->getListMapper()
      ->addColumn(new Column\Value("title"))
      ->addColumn(new Column\Date("updated", "d/m/Y"));
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("name"))
        ->add(Field\TextField::create("keyname"))
      ->end();
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  protected function formUpdateBefore(FormAdminEvent $formAdminEvent)
  {
    /** @var TrackingInterface|EntityInterface $object */
    $object = $formAdminEvent->getFormMapper()->getObject();
    if(!$object->getKeyname()) {
      $object->setKeyname($object->getName());
    }
  }
}