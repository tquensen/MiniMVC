<?php
/**
 * @property UserTable $_table
 * @method UserTable getTable()
 * @method UserCollection getCollection()
 *
 * @property integer $id
 * @property string $slug
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $salt
 * @property string $auth_token
 * @property string $role
 * @property integer $created_at
 * @property integer $updated_at
 * 
 */
abstract class UserBase extends MiniMVC_Model
{

}