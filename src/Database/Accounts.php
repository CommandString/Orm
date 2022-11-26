<?php

namespace CommandString\Orm\Database;

class Accounts extends Table
{
	public const ID = 'accounts.id';
	public const USERNAME = 'accounts.username';
	public const EMAIL = 'accounts.email';
	public const PASSWORD = 'accounts.password';
	public const ADMIN = 'accounts.admin';
	public const TERMS_AGREEMENT = 'accounts.terms-agreement';

	public string $name = 'accounts';
}
