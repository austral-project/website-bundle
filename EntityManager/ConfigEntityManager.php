<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\EntityManager;

use Austral\EntityTranslateBundle\Entity\Interfaces\EntityTranslateMasterInterface;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;
use Austral\WebsiteBundle\Repository\ConfigRepository;

use Austral\EntityBundle\EntityManager\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;

/**
 * Austral Config EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class ConfigEntityManager extends EntityManager
{

  /**
   * @var ConfigRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return ConfigInterface
   */
  public function create(array $values = array()): ConfigInterface
  {
    /** @var ConfigInterface|EntityTranslateMasterInterface $object */
    $object = parent::create($values);
    $object->setCurrentLanguage($this->currentLanguage);
    $object->createNewTranslateByLanguage();
    return $object;
  }

  /**
   * @param $keyname
   * @param \Closure|null $closure
   *
   * @return ConfigInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByKeyname($keyname, \Closure $closure = null): ?ConfigInterface
  {
    return $this->repository->retreiveByKeyname($keyname, $closure);
  }

  /**
   * @param string $language
   *
   * @return array|\Doctrine\Common\Collections\Collection
   * @throws QueryException
   */
  public function selectAllByIndexKeyname(string $language)
  {
    return $this->repository->selectAllByIndexKeyname($language);
  }

  /**
   * @param string $language
   *
   * @return array
   */
  public function selectArrayResultAll(string $language): array
  {
    return $this->repository->selectArrayResultAll($language);
  }

}
