<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Entity\Interfaces;

use Austral\EntitySeoBundle\Entity\Interfaces\EntitySeoInterface;

/**
 * Austral Page Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface PageParentInterface
{
  /**
   * Get refH1
   *
   * @return EntitySeoInterface|null
   */
  public function getPageParent(): ?EntitySeoInterface;

  /**
   * @param EntitySeoInterface $page
   *
   * @return $this
   */
  public function setPageParent(EntitySeoInterface $page): EntitySeoInterface;
}

    
    
      