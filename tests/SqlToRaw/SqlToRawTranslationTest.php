<?php

namespace tests\carlosV2\Translatte\SqlToRaw;

class SqlToRawTranslationTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itMatchesAnExactSql()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT * FROM table WHERE field = "value"');

        $this->assertTrue($translation->matches('SELECT * FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itDoesNotMatchNonExactSqls()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT * FROM table WHERE field = "value"');

        $this->assertFalse($translation->matches('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itMatchesNonExactSqlsWithPlaceholders()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $this->assertTrue($translation->matches('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itProvidesNoArgumentsIfNoPlaceholdersWhereProvided()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT * FROM table WHERE field = "value"');

        $this->assertEquals(array(), $translation->translate('SELECT * FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itProvidesDefaultArgumentsIfNoPlaceholdersWhereProvidedAndAnArgumentWithDefaultValueWasExpected()
    {
        $translation = new DefaultFieldArgSqlToRawTestTranslation('SELECT * FROM table WHERE field = "value"');

        $this->assertEquals(array('default'), $translation->translate('SELECT * FROM table WHERE field = "value"'));
    }

    /** @test @expectedException carlosV2\Translatte\SqlToRaw\Exception\ArgumentNotFoundException */
    public function itThrowAnExceptionIfNoPlaceholdersWhereProvidedButAnArgumentWasExpected()
    {
        $translation = new FieldArgSqlToRawTestTranslation('SELECT * FROM table WHERE field = "value"');

        $translation->translate('SELECT * FROM table WHERE field = "value"');
    }

    /** @test */
    public function itProvidesNoArgumentsIfTheDiscardPlaceholderIsProvided()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = "value"');

        $this->assertEquals(array(), $translation->translate('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itProvidesDefaultArgumentsIfTheDiscardPlaceholderIsProvidedAndAnArgumentWithDefaultValueWasExpected()
    {
        $translation = new DefaultFieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = "value"');

        $this->assertEquals(array('default'), $translation->translate('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test @expectedException carlosV2\Translatte\SqlToRaw\Exception\ArgumentNotFoundException */
    public function itThrowAnExceptionIfTheDiscardPlaceholderWasProvidedButAnArgumentWasExpected()
    {
        $translation = new FieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = "value"');

        $translation->translate('SELECT id FROM table WHERE field = "value"');
    }

    /** @test */
    public function itProvidesNoArgumentsIfPlaceholdersAreProvidedButNoArgumentIsExpected()
    {
        $translation = new NoArgsSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $this->assertEquals(array(), $translation->translate('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itProvidesTheArgumentsIfPlaceholdersAreProvidedAndAnArgumentWithDefaultValueIsExpected()
    {
        $translation = new DefaultFieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $this->assertEquals(array('value'), $translation->translate('SELECT id FROM table WHERE field = "value"'));
    }

    /** @test */
    public function itProvidesTheArgumentsIfPlaceholdersAreProvidedAndArgumentsAreExpected()
    {
        $translation = new FieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $this->assertEquals(array('value'), $translation->translate('SELECT id FROM table WHERE field = "value"'));
    }

    public function dataTypesProvider()
    {
        return array(
            array('SELECT id FROM table WHERE field = "value"', 'value'),
            array("SELECT id FROM table WHERE field = 'value'", 'value'),
            array('SELECT id FROM table WHERE field = 123', 123),
            array('SELECT id FROM table WHERE field = null', null),
            array('SELECT id FROM table WHERE field = true', true),
            array('SELECT id FROM table WHERE field = false', false),
        );
    }

    /** @test @dataProvider dataTypesProvider */
    public function itProvidesTheCorrectArgumentType($query, $field)
    {
        $translation = new FieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $this->assertSame(array($field), $translation->translate($query));
    }

    /** @test @expectedException carlosV2\Translatte\SqlToRaw\Exception\UnknownValueTypeException */
    public function itThrowsAnExceptionIfTheFoundValueCannotBeCasted()
    {
        $translation = new FieldArgSqlToRawTestTranslation('SELECT {*} FROM table WHERE field = {field}');

        $translation->translate('SELECT id FROM table WHERE field = value');
    }
}
