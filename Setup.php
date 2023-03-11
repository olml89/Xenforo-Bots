<?php declare(strict_types=1);

namespace olml89\Subscriptions;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = []): void
	{
        $this->schemaManager()->createTable('xf_subscriptions', function(Create $table) : void {
            $table->addColumn('id', 'int')->primaryKey()->autoIncrement();
            $table->addColumn('user_id', 'int')->nullable(FALSE);
            $table->addColumn('token', 'varchar', 32)->nullable(FALSE);
            $table->addColumn('webhook', 'varchar', 255)->nullable(FALSE);
            $table->addUniqueKey('user_id');
        });
	}

	public function upgrade(array $stepParams = []): void
	{
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array $stepParams = []): void
	{
        $this->schemaManager()->dropTable('xf_subscriptions');
	}
}
