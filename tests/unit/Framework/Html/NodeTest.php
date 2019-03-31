<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

class NodeTest extends NodeTestCase
{
    public function testGetRawContent()
    {
        $rawContent = 'A';

        $sut = $this->createNode(new Node(new Node($rawContent)));

        $actualResult = $sut->getRawContent();

        $this->assertSame($rawContent, $actualResult);
    }

    public function isMatchProvider(): array
    {
        return [
            'INode-no-intent'               => [INode::class, [], true],
            'INode-foo-intent'              => [INode::class, ['foo'], true],
            'INode-bar-intent'              => [INode::class, ['bar'], true],
            'INode-foo-and-bar-intent'      => [INode::class, ['foo', 'bar'], true],
            'fail-IComponent-foo-intent'    => [IComponent::class, ['foo'], false],
            'fail-Component-foo-intent'     => [Component::class, ['foo'], false],
            'fail-INode-baz-intent'         => [INode::class, ['baz'], false],
            'fail-INode-foo-and-baz-intent' => [INode::class, ['foo', 'baz'], false],
            'Node-foo-intent'               => [Node::class, ['foo'], true],
        ];
    }
}
