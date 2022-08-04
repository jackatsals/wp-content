<?php if (!empty($settings->books)) : ?>
    <div class="cdlc-book-carousel not-prose">
        <div class="cdlc-book-carousel__wrapper">
            <?php foreach ($settings->books as $book) : ?>
                <div class="cdlc-book-carousel__item">
                    <article>
                        <?php if (!empty($book->cover)) : ?>
                            <img src="<?php echo wp_get_attachment_url($book->cover); ?>" alt="" />
                        <?php endif; ?>
                        <h3>
                            <a href="<?php echo esc_url($book->link); ?>" aria-label="<?php echo $module->getBookScreenReaderText($book); ?>"><?php echo $book->title; ?></a>
                        </h3>
                        <?php if (!empty($book->author)) : ?>
                            <span><?php echo $book->author; ?></span>
                        <?php endif; ?>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
