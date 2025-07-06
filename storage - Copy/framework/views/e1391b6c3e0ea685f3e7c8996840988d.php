<?php if($paginator->hasPages()): ?>
    <div class="row align-items-center">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" role="status" aria-live="polite">
                <p class="small text-muted mb-0">
                    <?php echo __('Showing'); ?>

                    <span class="fw-semibold text-primary"><?php echo e($paginator->firstItem()); ?></span>
                    <?php echo __('to'); ?>

                    <span class="fw-semibold text-primary"><?php echo e($paginator->lastItem()); ?></span>
                    <?php echo __('of'); ?>

                    <span class="fw-semibold text-primary"><?php echo e($paginator->total()); ?></span>
                    <?php echo __('results'); ?>

                </p>
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination justify-content-end mb-0">
                    
                    <?php if($paginator->onFirstPage()): ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-double-left"></i>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->url(1)); ?>" rel="first" title="First Page">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    
                    <?php if($paginator->onFirstPage()): ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-left"></i>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" title="Previous Page">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(is_string($element)): ?>
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><?php echo e($element); ?></span>
                            </li>
                        <?php endif; ?>

                        
                        <?php if(is_array($element)): ?>
                            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $paginator->currentPage()): ?>
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link"><?php echo e($page); ?></span>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" title="Next Page">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-right"></i>
                            </span>
                        </li>
                    <?php endif; ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($paginator->url($paginator->lastPage())); ?>" rel="last" title="Last Page">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">
                                <i class="fas fa-angle-double-right"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\source_code\herbal-ecom\resources\views/pagination/bootstrap-5.blade.php ENDPATH**/ ?>