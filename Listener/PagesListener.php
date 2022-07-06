<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Listener;

use App\Entity\Austral\WebsiteBundle\Domain;
use App\Entity\Austral\WebsiteBundle\Page;
use App\Entity\Austral\WebsiteBundle\PageTranslate;
use Austral\EntitySeoBundle\Event\PagesEvent;

use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Entity\Interfaces\PageInterface;
use Austral\WebsiteBundle\EntityManager\DomainEntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Austral FormListener Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class PagesListener
{

  /**
   * @var Request|null
   */
  protected ?Request $request;

  /**
   * @var AuthorizationCheckerInterface
   */
  protected AuthorizationCheckerInterface $authorizationChecker;

  /**
   * @var DomainEntityManager
   */
  protected DomainEntityManager $domainEntityManager;

  /**
   * PagesListener constructor.
   *
   * @param RequestStack $request
   * @param AuthorizationCheckerInterface $authorizationChecker
   * @param DomainEntityManager $domainEntityManager
   */
  public function __construct(RequestStack $request, AuthorizationCheckerInterface $authorizationChecker, DomainEntityManager $domainEntityManager)
  {
    $this->request = $request->getCurrentRequest();
    $this->authorizationChecker = $authorizationChecker;
    $this->domainEntityManager = $domainEntityManager;
  }

  /**
   * @param PagesEvent $pagesEvent
   *
   * @throws Exception
   */
  public function selectObjects(PagesEvent $pagesEvent)
  {
  }

  /**
   * @param PagesEvent $pagesEvent
   *
   * @throws Exception
   *
   * DISABLED with error
   */
  public function _selectObjects(PagesEvent $pagesEvent)
  {
    $className = $pagesEvent->getClassname();
    $entityManager = $pagesEvent->getEntityManager();
    if(AustralTools::usedImplements($className, PageInterface::class) && $entityManager->getConnection()->getDriver()->getDatabasePlatform()->getName() === "postgresql")
    {
      $select = array();
      $select2 = array();
      $rsm = new ResultSetMapping();
      $rsm->addEntityResult($className, 'awp');
      foreach ($entityManager->getClassMetadata($className)->fieldMappings as $field) {
        $rsm->addFieldResult('awp', $field['columnName'], $field['fieldName']);
        $select[] = "awp.{$field['columnName']}";
        $select2[] = "T01.{$field['columnName']}";
      }
      foreach ($entityManager->getClassMetadata($className)->associationMappings as $field) {
        if(array_key_exists("joinColumns", $field))
        {
          $columnValue = AustralTools::first($field['joinColumns']);
          $select[] = "awp.{$columnValue['name']} as awp_parent_id";
          $select2[] = "T01.{$columnValue['name']} as awp1_parent_id";
        }
      }

      $rsm->addJoinedEntityResult(Domain::class, "awd", "awp", "domains");
      foreach ($entityManager->getClassMetadata(Domain::class)->fieldMappings as $field) {
        $rsm->addFieldResult('awd', "awd_{$field['columnName']}", $field['fieldName']);
        $select[] = "awd.{$field['columnName']} as awd_{$field['columnName']}";
        $select2[] = "awd1.{$field['columnName']} as awd1_{$field['columnName']}";
      }

      $rsm->addJoinedEntityResult(PageTranslate::class, "awpt", "awp", "translates");
      foreach ($entityManager->getClassMetadata(PageTranslate::class)->fieldMappings as $field) {
        $rsm->addFieldResult('awpt', "awpt_{$field['columnName']}", $field['fieldName']);
        $select[] = "awpt.{$field['columnName']} as awpt_{$field['columnName']}";
        $select2[] = "awpt1.{$field['columnName']} as awpt1_{$field['columnName']}";
      }

      $rsm->addJoinedEntityResult(Page::class, "awpc", "awp", "parent");
      $rsm->addFieldResult('awpc', 'awp_parent_id', 'id');

      $whereStatus = "";
      $whereStatus1 = "";
      if(!$this->authorizationChecker->isGranted("ROLE_ADMIN_ACCESS"))
      {
        $whereStatus = "AND (awpt.status = :status)";
        $whereStatus1 = "AND (awpt1.status = :status)";
      }
      else
      {
        $whereStatus = "AND (awpt.status = :status OR awpt.status = :statusDraft)";
        $whereStatus1 = "AND (awpt1.status = :status OR awpt1.status = :statusDraft)";
      }
//AND (awd.domain = :domain OR awd.sub_domains::text LIKE :domainLike)
      $query = $entityManager->createNativeQuery('
WITH RECURSIVE T0 AS
                (SELECT '.implode(",", $select).'
                FROM austral_website_page AS awp
                LEFT JOIN austral_website_domain awd on awp.id = awd.homepage_id
                LEFT JOIN austral_website_page_translate awpt on awp.id = awpt.master_id
                LEFT JOIN austral_website_page awpp on awp.parent_id = awpp.id
                WHERE awpt.language = :language
                '.$whereStatus.'
                UNION ALL
                SELECT '.implode(",", $select2).'
                FROM T0
                         JOIN austral_website_page AS T01
                                ON T0.id = T01.parent_id
                         JOIN austral_website_domain AS awd1
                                ON T0.awd_domain = awd1.domain
                         JOIN austral_website_page_translate AS awpt1
                                ON T01.id = awpt1.master_id
                         JOIN austral_website_page AS awpp1
                                ON T01.id = awpp1.id
                         WHERE awpt1.language = :language
                         '.$whereStatus1.'

               )
SELECT *
FROM T0;', $rsm);

      $host = $this->request->headers->get('host');

      if($this->authorizationChecker->isGranted("ROLE_ADMIN_ACCESS"))
      {
        $query->setParameter("statusDraft", "draft");
      }

      $query->setParameter("language", $this->request->attributes->get('language', $this->request->getLocale()))
        //->setParameter("domain", $host)
        ->setParameter("status", "published")
        ->setParameter("domainLike", '%"'.$host.'\\\%');
      $pagesEvent->setQuery($query);
    }
  }

}