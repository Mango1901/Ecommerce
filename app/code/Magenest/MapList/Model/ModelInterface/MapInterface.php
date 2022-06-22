<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/12/16
 * Time: 09:41
 */

namespace Magenest\MapList\Model\ModelInterface;

interface MapInterface
{
    const ID = 'map_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function getId();

    public function getTitle();

    public function getDescription();

    public function getCreatedAt();

    public function getUpdatedAt();

    public function setId($id);

    public function setTitle($title);

    public function setDescription($description);

    public function setCreatedAt($unixTime);

    public function setUpdatedAt($unixTime);
}
