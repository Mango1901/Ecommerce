<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 16:26
 */

namespace Magenest\MapList\Model\ModelInterface;

interface CategoryInterface
{
    const ID = 'category_id';
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
