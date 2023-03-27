<?php
namespace GDO\Aprilfools;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Date\Time;
use GDO\UI\GDT_Panel;

/**
 * @TODO Implement the april fools module. Collect ideas in the lang file.
 *
 * @version 7.0.1
 * @since 7.0.1
 * @author gizmore
 */
final class Module_Aprilfools extends GDO_Module
{

	public const ONLY_FIRST = 'first_april';
	public const ALWAYS = 'always_april';
	public const NEVER = 'never_april';
	public int $priority = 99;

	##############
	### Module ###
	##############

	public function onLoadLanguage(): void { $this->loadLanguage('lang/april'); }

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('teapot_easteregg')->initial('1'),
			GDT_EnumNoI18n::make('april_behaviour')->initial('only_april')->enumValues(self::ONLY_FIRST, self::ALWAYS, self::NEVER)->notNull(),
		];
	}

	public function isAprilMode(): bool
	{
		switch ($this->cfgApril())
		{
			case self::ONLY_FIRST:
				$date = Time::displayDateISO('de');
				$date = substr($date, 0, 6);
				return $date === '01.04.';
			case self::NEVER:
				return false;
			case self::ALWAYS:
				return true;
			default:
				return false;
		}
	}

	public function cfgApril(): string { return $this->getConfigValue('april_behaviour'); }

	###########
	### API ###
	###########

	/**
	 * Before any request, we check for the speacial teapot temperature header.
	 */
	public function hookBeforeRequest(Method $method, GDT_Response $response): void
	{
		if ($this->cfgTeapot())
		{
			$this->onTeapotEasteregg($response);
		}
	}

	#############################
	### Signup Password Taken ###
	#############################

	########################
	### Teapot Easteregg ###
	########################

	public function cfgTeapot(): bool { return $this->getConfigValue('teapot_easteregg'); }

	private function onTeapotEasteregg(GDT_Response $response): void
	{
		if ($temperature = $this->getTeapotTemperature())
		{
			$perfect = 'msg_april_tea_perfects';
			if ($temperature < 42)
			{
				$perfect = 'err_april_tea_too_cold';
			}
			elseif ($temperature > 65)
			{
				$perfect = 'err_april_tea_too_warm';
			}
			$response->addField(GDT_Panel::make()->text($perfect));
		}
	}

	private function getTeapotTemperature(): float
	{
		return 41;
	}

}
