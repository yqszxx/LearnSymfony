<?php

namespace yqszxx\AlipayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('alipay');

        $rootNode
            ->children()
                ->integerNode('partner')->cannotBeEmpty()->end()
                ->scalarNode('key')->cannotBeEmpty()->end()
                ->scalarNode('seller_email')->cannotBeEmpty()->end()
                ->enumNode('sign_type')->values(array('MD5','DSA','RSA'))->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
