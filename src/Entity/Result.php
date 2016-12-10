<?php   // src/Entity/Result.php

namespace MiW16\Results\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Result
 * @package MiW16\Results\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="results",
 *      indexes={
 *          @ORM\Index(name="FK_USER_ID_idx", columns={"user_id"})
 *      }
 *     )
 */
class Result implements \JsonSerializable
{
    const CLASS_NAME = __CLASS__;
    const DATE_FORMAT = 'Y/m/d H:i:s';
    const TIME_ATTRIBUTE = 'time';
    const ID_ATTRIBUTE = 'id';
    const USER_ATTRIBUTE = 'user';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="integer", nullable=false)
     */
    protected $result;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    protected $time;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Result constructor.
     *
     * @param int $result
     * @param User $user
     * @param \DateTime $time
     */
    public function __construct(int $result, User $user, \DateTime $time)
    {
        $this->id     = 0;
        $this->result = $result;
        $this->user   = $user;
        $this->time   = $time;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $result
     */
    public function setResult(int $result)
    {
        $this->result = $result;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString(): string
    {
        $attributes = get_object_vars($this);
        $toString = get_class($this) . PHP_EOL . '(' . PHP_EOL;

        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeName === Result::TIME_ATTRIBUTE)
                $toString .= '  [' . $attributeName . '] => ' . date_format($attributeValue, Result::DATE_FORMAT)
                    . PHP_EOL;
            else
                $toString .= '  [' . $attributeName . '] => ' . $attributeValue . PHP_EOL;
        }

        $toString .= ')';

        return $toString;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
