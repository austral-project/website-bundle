##php##
/*
 * This file is autogenerate and part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Austral\WebsiteBundle;
use Austral\WebsiteBundle\Entity\ConfigValueByDomain as BaseConfigValueByDomain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Config ValueByDomain Entity.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @ORM\Table(name="austral_website_config_value_by_domain")
 * @ORM\Entity(repositoryClass="Austral\WebsiteBundle\Repository\ConfigValueByDomainRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ConfigValueByDomain extends BaseConfigValueByDomain
{
  public function __construct()
  {
    parent::__construct();
  }
}
