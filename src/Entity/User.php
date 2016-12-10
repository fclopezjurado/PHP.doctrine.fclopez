<?php // src/Entity/User.php

namespace MiW16\Results\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UNIQ_TOKEN", columns={"token"}
 *          )
 *      }
 *     )
 * @ORM\Entity
 */
class User implements \JsonSerializable
{
    const CLASS_NAME = __CLASS__;
    const DATE_FORMAT = 'Y/m/d H:i:s';
    const LAST_LOGIN_ATTRIBUTE = 'lastLogin';
    const TOKEN_ATTRIBUTE = 'token';
    const ID_ATTRIBUTE = 'id';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=40, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=40, nullable=false)
     */
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 0;
        $this->username = '';
        $this->email = '';
        $this->enabled = false;
        $this->token = sha1(uniqid('', true));
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param \DateTime $lastLogin
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @inheritDoc
     */
    function __toString(): string
    {
        $attributes = get_object_vars($this);
        $toString = get_class($this) . PHP_EOL . '(' . PHP_EOL;

        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeName === User::LAST_LOGIN_ATTRIBUTE)
                $toString .= '  [' . $attributeName . '] => ' . date_format($attributeValue, User::DATE_FORMAT)
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
