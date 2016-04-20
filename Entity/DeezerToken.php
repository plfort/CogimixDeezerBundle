<?php
namespace Cogipix\CogimixDeezerBundle\Entity;
use Cogipix\CogimixCommonBundle\Entity\User;
use JMS\Serializer\Annotation as JMSSerializer;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="deezertoken")
 * @author plfort - Cogipix
 *
 */
class DeezerToken
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Cogipix\CogimixCommonBundle\Entity\User")
     * @var User $user
     * @JMSSerializer\Exclude()
     */

    protected $user;

    /**
     * @ORM\Column(type="string")
     * @var unknown_type
     */
    protected $accessToken;

    /**
     * @ORM\Column(type="string")
     * @var unknown_type
     */
    protected $userId;

    /**
     * @ORM\Column(type="integer")
     * @var unknown_type
     */
    protected $expiresIn;
    /**
     * @ORM\Column(type="datetime")
     * @var unknown
     */
    protected $dateCreated;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

}
