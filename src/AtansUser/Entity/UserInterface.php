<?php

namespace AtansUser\Entity;

use DateTime;

/**
 * User interface
 */
interface UserInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param  int $id
     * @return User
     */
    public function setId($id);

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set username
     *
     * @param  string $username
     * @return User
     */
    public function setUsername($username);

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param  string $email
     * @return User
     */
    public function setEmail($email);

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set password
     *
     * @param  string $password
     * @return User
     */
    public function setPassword($password);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param  string $status
     * @return User
     */
    public function setStatus($status);

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated();

    /**
     * Set created
     *
     * @param  DateTime $created
     * @return User
     */
    public function setCreated(DateTime $created);
}