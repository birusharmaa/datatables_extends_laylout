<?php

declare (strict_types=1);
namespace Rector\Defluent\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Defluent\Contract\ValueObject\FirstCallFactoryAwareInterface;
use Rector\NodeCollector\NodeCollector\NodeRepository;
use Rector\NodeTypeResolver\Node\AttributeKey;
final class SameClassMethodCallAnalyzer
{
    /**
     * @var \Rector\NodeCollector\NodeCollector\NodeRepository
     */
    private $nodeRepository;
    public function __construct(\Rector\NodeCollector\NodeCollector\NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }
    /**
     * @param MethodCall[] $chainMethodCalls
     */
    public function haveSingleClass(array $chainMethodCalls) : bool
    {
        // are method calls located in the same class?
        $classOfClassMethod = [];
        foreach ($chainMethodCalls as $chainMethodCall) {
            $classMethod = $this->nodeRepository->findClassMethodByMethodCall($chainMethodCall);
            if ($classMethod instanceof \PhpParser\Node\Stmt\ClassMethod) {
                $classOfClassMethod[] = $classMethod->getAttribute(\Rector\NodeTypeResolver\Node\AttributeKey::CLASS_NAME);
            } else {
                $classOfClassMethod[] = null;
            }
        }
        $uniqueClasses = \array_unique($classOfClassMethod);
        return \count($uniqueClasses) < 2;
    }
    /**
     * @param string[] $calleeUniqueTypes
     */
    public function isCorrectTypeCount(array $calleeUniqueTypes, \Rector\Defluent\Contract\ValueObject\FirstCallFactoryAwareInterface $firstCallFactoryAware) : bool
    {
        if ($calleeUniqueTypes === []) {
            return \false;
        }
        // in case of factory method, 2 methods are allowed
        if ($firstCallFactoryAware->isFirstCallFactory()) {
            return \count($calleeUniqueTypes) === 2;
        }
        return \count($calleeUniqueTypes) === 1;
    }
}
