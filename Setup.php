<?php declare(strict_types=1);

namespace olml89\XenforoSubscriptions;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = []): void
	{
        $this->createTable('olml89_xenforo_subscriptions_bot', function (Create $table)
        {
            $table->addColumn('bot_id', 'varchar', 36)->primaryKey();
            $table->addColumn('user_id', 'int')->nullable(false);
            $table->addColumn('api_key_id', 'int')->nullable(false);
            $table->addColumn('created_at', 'int')->nullable(false);
        });

        $this->createTable('olml89_xenforo_subscriptions_bot_subscription', function (Create $table)
        {
            $table->addColumn('subscription_id', 'varchar', 36)->primaryKey();
            $table->addColumn('bot_id', 'varchar', 36)->nullable(false);
            $table->addColumn('webhook', 'varchar', 255);
            $table->addColumn('subscribed_at', 'int')->nullable(false);
            $table->addUniqueKey('bot_id', 'idx_bot_id_webhook')->addColumn('webhook');
        });
	}

	public function upgrade(array $stepParams = []): void
	{
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array $stepParams = []): void
	{
        $this->schemaManager()->dropTable('olml89_xenforo_subscriptions_bot');
        $this->schemaManager()->dropTable('olml89_xenforo_subscriptions_subscription');
	}
}
