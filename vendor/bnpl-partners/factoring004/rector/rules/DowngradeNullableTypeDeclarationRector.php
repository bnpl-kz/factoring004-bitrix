<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004RectorRules;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\BetterPhpDocParser\PhpDocParser\PhpDocFromTypeDeclarationDecorator;
use Rector\Core\NodeAnalyzer\ParamAnalyzer;

class DowngradeNullableTypeDeclarationRector extends \Rector\Core\Rector\AbstractRector
{
    private PhpDocTypeChanger $phpDocTypeChanger;
    private PhpDocFromTypeDeclarationDecorator $phpDocFromTypeDeclarationDecorator;
    private ParamAnalyzer $paramAnalyzer;

    public function __construct(
        PhpDocTypeChanger $phpDocTypeChanger,
        PhpDocFromTypeDeclarationDecorator $phpDocFromTypeDeclarationDecorator,
        ParamAnalyzer $paramAnalyzer
    ) {
        $this->phpDocTypeChanger = $phpDocTypeChanger;
        $this->phpDocFromTypeDeclarationDecorator = $phpDocFromTypeDeclarationDecorator;
        $this->paramAnalyzer = $paramAnalyzer;
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

    public function getRuleDefinition(): \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \Symplify\RuleDocGenerator\ValueObject\RuleDefinition(
            'Remove the nullable type params, add @param tags instead', [
            new \Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(?string $input): ?string
    {
    }
}
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @return string|null
     */
    public function run(string $input = null)
    {
    }
}
CODE_SAMPLE
            ),
        ]
        );
    }

    /**
     * @param ClassMethod|Function_|Closure $node
     */
    public function refactor(Node $node): ?Node
    {
        $hasChanged = \false;
        foreach ($node->params as $param) {
            if ($this->refactorParamType($param, $node)) {
                $hasChanged = \true;
            }
        }
        if ($node->returnType instanceof \PhpParser\Node\NullableType) {
            $this->phpDocFromTypeDeclarationDecorator->decorate($node);
            $hasChanged = \true;
        }
        if ($hasChanged) {
            return $node;
        }
        return null;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_|\PhpParser\Node\Expr\Closure $functionLike
     */
    private function refactorParamType(Param $param, $functionLike): bool
    {
        if (!$this->paramAnalyzer->isNullable($param)) {
            return \false;
        }

        $this->decorateWithDocBlock($functionLike, $param);

        if ($this->paramAnalyzer->hasDefaultNull($param)) {
            $param->type = $param->type->type;
        } else {
            $param->type = null;
        }

        return \true;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_|\PhpParser\Node\Expr\Closure $functionLike
     */
    private function decorateWithDocBlock($functionLike, Param $param): void
    {
        if ($param->type === null) {
            return;
        }
        $type = $this->staticTypeMapper->mapPhpParserNodePHPStanType($param->type);
        $paramName = $this->getName($param->var);
        if ($paramName === null) {
            throw new \Rector\Core\Exception\ShouldNotHappenException();
        }
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($functionLike);
        $this->phpDocTypeChanger->changeParamType($phpDocInfo, $type, $param, $paramName);
    }
}
