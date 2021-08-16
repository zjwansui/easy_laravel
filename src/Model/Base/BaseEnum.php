<?php


namespace Zjwansui\EasyLaravel\Model\Base;


class BaseEnum
{
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;


    public const DELETED_YES = 1;
    public const DELETED_NO = -1;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const CREATED_BY = 'created_by';
    public const UPDATED_BY = 'updated_by';

}
