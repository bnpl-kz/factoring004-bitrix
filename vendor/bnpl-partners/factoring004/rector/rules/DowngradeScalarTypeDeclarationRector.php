<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004RectorRules;

use PhpParser\Node;
use Rector\BetterPhpDocParser\PhpDocParser\PhpDocFromTypeDeclarationDecorator;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class DowngradeScalarTypeDeclarationRector extends AbstractRector
{
    private PhpDocFromTypeDeclarationDecorator $phpDocFromTypeDeclarationDecorator;

    public function __construct(PhpDocFromTypeDeclarationDecorator $phpDocFromTypeDeclarationDecorator)
    {
        $this->phpDocFromTypeDeclarationDecorator = $phpDocFromTypeDeclarationDecorator;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
            \PhpParser\Node\Stmt\Function_::class,
            \PhpParser\Node\Stmt\ClassMethod::class,
            \PhpParser\Node\Expr\Closure::class,
        ];
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove the type params and return type, add @param and @return tags instead', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(string $input): string
    {
    }
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @param string $input
     * @return string
     */
    public function run($input)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    public function refactor(Node $node): ?Node
    {
        /** @var \PhpParser\Node\Param $param */
        foreach ($node->params as $param) {
            if ($param->type === null) {
                continue;
            }

            $this->phpDocFromTypeDeclarationDecorator->decorateParam($param, $node, [
                \PHPStan\Type\StringType::class,
                \PHPStan\Type\IntegerType::class,
                \PHPStan\Type\BooleanType::class,
                \PHPStan\Type\FloatType::class,
            ]);
        }

        if ($node->returnType === null) {
            return null;
        }

        $info = $this->phpDocInfoFactory->createFromNode($node);

        if ($info->hasByName('@return')) {
            $node->returnType = null;
        } else {
            $this->phpDocFromTypeDeclarationDecorator->decorate($node);
        }

        return $node;
    }
}
