<?php

namespace tests\carlosV2\Translatte;

use carlosV2\Translatte\Translator;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Translator
     */
    private $translator;

    protected function setUp()
    {
        $this->translator = new Translator();
    }

    /** @test */
    public function itTranslatesAMessage()
    {
        $translation = $this->prophesize('carlosV2\Translatte\TranslationInterface');
        $translation->matches('message')->willReturn(true);
        $translation->translate('message')->willReturn('mensaje');

        $this->translator->addTranslation($translation->reveal());

        $this->assertEquals('mensaje', $this->translator->translate('message'));
    }

    /** @test @expectedException carlosV2\Translatte\Exception\TranslationNotFoundException */
    public function itThrowsAnExceptionIfItCannotFindATranslation()
    {
        $translation = $this->prophesize('carlosV2\Translatte\TranslationInterface');
        $translation->matches('message')->willReturn(false);

        $this->translator->addTranslation($translation->reveal());

        $this->translator->translate('message');
    }
}
