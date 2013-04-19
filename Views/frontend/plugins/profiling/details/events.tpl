<div class="event-wrapper">
    <ul class="event-list">
        {foreach $profiling.events as $name => $event}
        <li class="event-item">
            <div class="inner-item">
                <div class="item-icon">
                    <div class="event-type">{$event.type}</div>
                </div>
                <div class="item-content">
                    <div class="event-name">{$name} <span class="duration"> ({$event.duration} ms)</span></div>
                    <ul class="listener-ul">
                        {foreach $event.listeners as $listener}
                            <li class="listener-item">
                                <span class="class">{$listener.class}</span>::
                                <span class="function">{$listener.function}</span>::
                                <span class="position">{$listener.position}</span>
                            </li>
                        {/foreach}
                    </ul>
                </div>
                <div class="clear"></div>

                <div class="item-returns">
                    <div class="event-params">
                        <p class="returns-headline">Parameters</p>
                        {include file="frontend/plugins/profiling/details/array.tpl" array=$event.params}
                    </div>
                    <div class="returns-before">
                        <p class="returns-headline">Before-Listener-Triggered</p>
                        {include file="frontend/plugins/profiling/details/array.tpl" array=$event.returns.0}
                    </div>
                    <div class="returns-after">
                        <p class="returns-headline">After-Listener-Triggered</p>
                        {include file="frontend/plugins/profiling/details/array.tpl" array=$event.returns.1}
                    </div>
                    <div class="clear"></div>
                </div>

            </div>
        </li>
        {/foreach}
    </ul>
</div>