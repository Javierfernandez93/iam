<?php

namespace JFStudio;

class LotajeParser
{	
	public function getLotage(float|string $lotage = null)
	{
		if(isset($lotage))
		{
			return (float)$lotage;
		}
	}
}