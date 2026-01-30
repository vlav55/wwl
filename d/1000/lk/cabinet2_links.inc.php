<!-- Bootstrap Modal -->
<div class="modal fade" id="partnerLinksModal" tabindex="-1" role="dialog" aria-labelledby="partnerLinksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="partnerLinksModalLabel">
                    <i class="fa fa-link mr-2"></i>Партнерские ссылки
                    <?php if($totalLinks > 0): ?>
                    <span class="badge badge-light ml-2"><?=$totalLinks?></span>
                    <?php endif; ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <?php if($totalLinks > 0): ?>
                
                <?php
                // VIP Partner Section (if applicable)
                if($ctrl_id==1) {
                    $pattern = '/(?:RewriteRule\s\^)(.*?)\/\?\$.*?https:\/\/wwl\.winwinland\.ru\/(.*?)\/?bc=(.*?)(?:\s\[R=301,L])/';
                    $arr=file("/var/www/vlav/data/www/wwl/winwinland/.htaccess");
                    $suf=false;
                    foreach($arr AS $str) {
                        if(preg_match($pattern,trim($str),$m)) {
                            if($bc==$m[3]) {
                                $suf=$m[1];
                            }
                        }
                    }
                    if($suf) {
                ?>
                <!-- VIP Partner Card -->
                <div class='card border-primary mb-4 shadow-sm'>
                    <div class='card-header bg-info text-white'>
                        <div class='d-flex align-items-center'>
                            <i class='fa fa-crown fa-2x mr-3'></i>
                            <div>
                                <h5 class='mb-0'>Вы VIP партнер</h5>
                                <small class='opacity-75'>Основная ссылка для рекомендаций ВИНВИНЛЭНД</small>
                            </div>
                        </div>
                    </div>
                    <div class='card-body'>
                        <div class='input-group'>
                            <input type='text' class='form-control border-primary' id='link_vip' value='https://winwinland.ru/<?=$suf?>' readonly>
                            <div class='input-group-append'>
                                <button class='btn btn-outline-primary' onclick="copySpanContent('link_vip')" title='Скопировать ссылку'>
                                    <i class='fa fa-copy'></i>
                                </button>
                                <a href='https://winwinland.ru/<?=$suf?>' class='btn btn-primary' target='_blank' title='Открыть ссылку'>
                                    <i class='fa fa-external-link'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
                
                <div class='row'>
                    <?php
                    // VK Links
                    if($link) {
                    ?>
                    <!-- VK Card -->
                    <div class='col-md-6 col-lg-4 mb-4'>
                        <div class='card h-100 border shadow-sm'>
                            <div class='card-header bg-white'>
                                <div class='d-flex align-items-center'>
                                    <div class='bg-vk text-white rounded-circle p-2 mr-3' style='background-color: #4C75A3; width: 40px; height: 40px;'>
                                        <i class='fa fa-vk'></i>
                                    </div>
                                    <div>
                                        <h6 class='mb-0'>ВКонтакте</h6>
                                        <small class='text-muted'>Партнерская ссылка</small>
                                    </div>
                                </div>
                            </div>
                            <div class='card-body'>
                                <p class='card-text small text-muted mb-3'>Ваша партнерская ссылка ВКонтакте</p>
                                <div class='input-group input-group-sm mb-3'>
                                    <input type='text' class='form-control' id='vk1' value='<?=$link?>' readonly>
                                    <div class='input-group-append'>
                                        <button class='btn btn-outline-secondary' onclick="copySpanContent('vk1')" title='Скопировать'>
                                            <i class='fa fa-copy'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class='card-footer bg-white border-top-0'>
                                <a href='<?=$link?>' class='btn btn-sm btn-outline-primary btn-block' target='_blank'>
                                    <i class='fa fa-external-link mr-1'></i>Открыть
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    
                    if($link1) {
                    ?>
                    <!-- VK Partner Card -->
                    <div class='col-md-6 col-lg-4 mb-4'>
                        <div class='card h-100 border shadow-sm'>
                            <div class='card-header bg-white'>
                                <div class='d-flex align-items-center'>
                                    <div class='bg-vk text-white rounded-circle p-2 mr-3' style='background-color: #4C75A3; width: 40px; height: 40px;'>
                                        <i class='fa fa-vk'></i>
                                    </div>
                                    <div>
                                        <h6 class='mb-0'>ВКонтакте</h6>
                                        <small class='text-success'><i class='fa fa-user-plus mr-1'></i>Партнерский лэндинг</small>
                                    </div>
                                </div>
                            </div>
                            <div class='card-body'>
                                <p class='card-text small text-muted mb-3'>Приглашение в партнерскую программу ВКонтакте</p>
                                <div class='input-group input-group-sm mb-3'>
                                    <input type='text' class='form-control' id='vk2' value='<?=$link1?>' readonly>
                                    <div class='input-group-append'>
                                        <button class='btn btn-outline-secondary' onclick="copySpanContent('vk2')" title='Скопировать'>
                                            <i class='fa fa-copy'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class='card-footer bg-white border-top-0'>
                                <a href='<?=$link1?>' class='btn btn-sm btn-outline-success btn-block' target='_blank'>
                                    <i class='fa fa-user-plus mr-1'></i>Открыть
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    
                    // Telegram/Other Links
                    $res=$db->query("SELECT * FROM lands WHERE del=0 AND fl_not_disp_in_cab=0");
                    while($r=$db->fetch_assoc($res)) {
                        $arr = parse_url($r['land_url']);
                        $link= (isset($arr['scheme']) ? $arr['scheme'] . '://' : '') .
                               (isset($arr['host']) ? $arr['host'] : '') .
                               (isset($arr['port']) ? ':' . $arr['port'] : '') .
                               (isset($arr['path']) ? $arr['path'] : '');
                        if(isset($arr['query']))
                            $link.="?{$arr['query']}$bc";
                        else
                            $link.="/?bc=$bc";
                        
                        $isPartnerLand = $r['fl_partner_land'];
                        $cardColor = $isPartnerLand ? 'border-success' : 'border-info';
                        $badgeColor = $isPartnerLand ? 'badge-success' : 'badge-info';
                        $buttonColor = $isPartnerLand ? 'btn-success' : 'btn-primary';
                        $buttonText = $isPartnerLand ? 'Пригласить партнера' : 'Открыть';
                        $buttonIcon = $isPartnerLand ? 'fa-user-plus' : 'fa-external-link';
                    ?>
                    <!-- Telegram/Other Link Card -->
                    <div class='col-md-6 col-lg-4 mb-4'>
                        <div class='card h-100 border <?=$cardColor?> shadow-sm'>
                            <div class='card-header bg-white'>
                                <div class='d-flex align-items-center'>
                                    <div class='bg-telegram text-white rounded-circle p-2 mr-3' style='background-color: #0088cc; width: 40px; height: 40px;'>
                                        <i class='fa fa-telegram'></i>
                                    </div>
                                    <div>
                                        <h6 class='mb-0'><?=htmlspecialchars($r['land_name'])?></h6>
                                        <?php if($isPartnerLand): ?>
                                        <small class='text-success'><i class='fa fa-user-plus mr-1'></i>Партнерский лэндинг</small>
                                        <?php else: ?>
                                        <small class='text-muted'>Реферальная ссылка</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class='card-body'>
                                <p class='card-text small text-muted mb-3'>
                                    <?php if($isPartnerLand): ?>
                                    Приглашение в партнерскую программу
                                    <?php else: ?>
                                    Партнерская ссылка для рекомендаций
                                    <?php endif; ?>
                                </p>
                                <div class='input-group input-group-sm mb-3'>
                                    <input type='text' class='form-control' id='link_<?=$r['id']?>' value='<?=$link?>' readonly>
                                    <div class='input-group-append'>
                                        <button class='btn btn-outline-secondary' onclick="copySpanContent('link_<?=$r['id']?>')" title='Скопировать'>
                                            <i class='fa fa-copy'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class='card-footer bg-white border-top-0'>
                                <a href='<?=$link?>' class='btn btn-sm <?=$buttonColor?> btn-block' target='_blank'>
                                    <i class='fa <?=$buttonIcon?> mr-1'></i><?=$buttonText?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                
                <!-- Information Section -->
                <div class='row mt-4'>
                    <div class='col-12'>
                        <div class='alert alert-info'>
                            <h6><i class='fa fa-info-circle mr-2'></i>Как использовать ссылки</h6>
                            <ul class='mb-0 pl-3'>
                                <li>Разместите ссылки в соцсетях и чатах</li>
                                <li>Передайте друзьям и знакомым</li>
                                <li>Расскажите о продуктах компании</li>
                                <li>По партнерским ссылкам ваши знакомые закрепляются за вами</li>
                            </ul>
                        </div>
                        
                        <?php if($link1 || $db->dlookup("id","lands","del=0 AND fl_partner_land=1 AND fl_not_disp_in_cab=0")): ?>
                        <div class='alert alert-success'>
                            <h6><i class='fa fa-user-plus mr-2'></i>Партнерские лэндинги</h6>
                            <p class='mb-0'>
                                По ссылкам с отметкой <span class='badge badge-success'><i class='fa fa-user-plus mr-1'></i>Партнерский лэндинг</span> 
                                ваши знакомые смогут не только зарегистрироваться, но и принять участие в партнерской программе, как и вы.
                                Вы будете получать вознаграждение от их продаж по второму уровню бонусной системы.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa fa-link fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Нет доступных партнерских ссылок</h5>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" onclick="copyAllLinks()">
                    <i class="fa fa-copy mr-2"></i>Копировать все ссылки
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add some CSS for better modal display -->
<style>
    .modal-xl {
        max-width: 95%;
    }
    @media (max-width: 767px) {
        .modal-xl {
            max-width: 100%;
            margin: 10px;
        }
        .card-header .bg-vk,
        .card-header .bg-telegram {
            width: 35px !important;
            height: 35px !important;
            padding: 7px !important;
        }
        .input-group-sm input {
            font-size: 12px;
        }
    }
    @media (max-width: 575px) {
        .input-group-sm input {
            font-size: 11px;
        }
        .modal-body {
            padding: 15px;
        }
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>

<!-- JavaScript for copying all links -->
<script>
function copyAllLinks() {
    // Get all input fields with links
    const linkInputs = document.querySelectorAll('#partnerLinksModal input[type="text"]');
    let allLinks = '';
    
    linkInputs.forEach((input, index) => {
        allLinks += input.value + '\n';
    });
    
    // Copy to clipboard
    navigator.clipboard.writeText(allLinks).then(() => {
        alert('Все ссылки скопированы в буфер обмена!');
    }).catch(err => {
        console.error('Ошибка при копировании: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = allLinks;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Все ссылки скопированы в буфер обмена!');
        } catch (err) {
            console.error('Fallback ошибка: ', err);
            alert('Не удалось скопировать ссылки');
        }
        document.body.removeChild(textArea);
    });
}

// Make sure copySpanContent function exists (from your original code)
function copySpanContent(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.select();
        element.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        // Optional: Show tooltip or notification
        const originalTitle = element.getAttribute('data-original-title') || element.title;
        alert('Ссылка скопирована!');
    }
}
</script>
