<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\WebsiteBundle\Handler\Interfaces;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\SeoInterface;
use Austral\SeoBundle\Entity\Interfaces\UrlParameterInterface;
use Austral\NotifyBundle\Mercure\Mercure;
use Austral\WebsiteBundle\Services\ConfigVariable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Austral Website Handler Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface WebsiteHandlerInterface
{

  /**
   * @param ConfigVariable $configVariable
   *
   * @return $this
   */
  public function setConfigVariable(ConfigVariable $configVariable): WebsiteHandlerInterface;

  /**
   * @param Mercure|null $mercure
   *
   * @return $this
   */
  public function setMercure(?Mercure $mercure): WebsiteHandlerInterface;

  /**
   * @return $this
   */
  public function initHandler(): WebsiteHandlerInterface;

  /**
   * executeResponse
   *
   * @param Response $response
   * @return WebsiteHandlerInterface
   */
  public function executeResponse(Response $response): WebsiteHandlerInterface;

  /**
   * @param string $handlerMethod
   *
   * @return $this
   */
  public function setHandlerMethod(string $handlerMethod): WebsiteHandlerInterface;

  /**
   * @param EntityInterface|null $page
   *
   * @return $this
   */
  public function setPage(?EntityInterface $page): WebsiteHandlerInterface;

  /**
   * @return EntityInterface|SeoInterface|null
   */
  public function getPage(): ?EntityInterface;

  /**
   * @return array
   */
  public function getLibraries(): array;

  /**
   * @param array $libraries
   *
   * @return $this
   */
  public function setLibraries(array $libraries): WebsiteHandlerInterface;

  /**
   * @param UrlParameterInterface|EntityInterface|null $urlParameter
   *
   * @return $this
   */
  public function setUrlParameter(?UrlParameterInterface $urlParameter): WebsiteHandlerInterface;

  /**
   * @return UrlParameterInterface|EntityInterface|null
   */
  public function getUrlParameter(): ?UrlParameterInterface;

}