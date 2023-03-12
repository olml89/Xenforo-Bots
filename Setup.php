<?php declare(strict_types=1);

namespace olml89\Subscriptions;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = []): void
	{
        $this->schemaManager()->createTable('xf_subscriptions', function(Create $table): void {
            $table->addColumn('id', 'varchar', 36)->primaryKey();
            $table->addColumn('user_id', 'int')->nullable(false);
            $table->addColumn('webhook', 'varchar', 255)->nullable(false);
            $table->addColumn('token', 'varchar', 32)->nullable(false);
            $table->addUniqueKey('user_id', 'idx_user_id_webhook')->addColumn('webhook');
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
