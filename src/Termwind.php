<?php

declare(strict_types=1);

namespace Ladoc;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\style;

readonly class Termwind
{
    public function __construct(private Styles $styles)
    {
        //
    }


    public function loadStyles(): bool
    {
        $styles = $this->styles->all();

        foreach ($styles as $name => $style) {
            style($name)->apply($style);
        }

        return true;
    }


    /**
     * @codeCoverageIgnore
     */
    public function render(string $html): void
    {
        $this->loadStyles();
        render(sprintf('<div>%s</div>', $html));
    }

    public function renderToString(string $html): string
    {
        $this->loadStyles();

        $buffer = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);

        renderUsing($buffer);
        render(sprintf('<div>%s</div>', $html));
        renderUsing(null);

        return $buffer->fetch();
    }

}
