// #########################################################
// Extbase Configuration
// #########################################################

config.tx_extbase.persistence {

	classes {

        RKW\RkwRegistration\Domain\Model\BackendUser {
            subclasses {
                Tx_RkwSurvey_BackendUser = RKW\RkwSurvey\Domain\Model\BackendUser
            }
        }

		RKW\RkwSurvey\Domain\Model\BackendUser {
			mapping {

				# tablename
				tableName = be_users

				# if we set an value, we filter by it
				# if do not set anything, all records are found
				recordType =

			}
		}
	}
}

plugin.tx_rkwsurvey_survey {
	view {
		templateRootPaths.0 = EXT:rkw_survey/Resources/Private/Templates/
		templateRootPaths.1 = {$plugin.tx_rkwsurvey_survey.view.templateRootPath}
		partialRootPaths.0 = EXT:rkw_survey/Resources/Private/Partials/
		partialRootPaths.1 = {$plugin.tx_rkwsurvey_survey.view.partialRootPath}
		layoutRootPaths.0 = EXT:rkw_survey/Resources/Private/Layouts/
		layoutRootPaths.1 = {$plugin.tx_rkwsurvey_survey.view.layoutRootPath}
	}
	persistence {
		storagePid = {$plugin.tx_rkwsurvey_survey.persistence.storagePid}
		#recursive = 1
	}
	features {
		#skipDefaultArguments = 1
	}
	mvc {
		#callDefaultActionIfActionCantBeResolved = 1
	}

    settings {
        showSurveyNameIntroExtro = {$plugin.tx_rkwsurvey_survey.settings.showSurveyNameIntroExtro}
        showSurveyNameProgess = {$plugin.tx_rkwsurvey_survey.settings.showSurveyNameProgess}
    }
}

page.includeJSFooter.txRkwSurvey = EXT:rkw_survey/Resources/Public/Scripts/Survey.js
# page.includeCSS.txRkwQuickcheck = EXT:rkw_survey/Resources/Public/Styles/Survey.css



# Module configuration
module.tx_rkwsurvey_web_rkwsurveyevaluation {
	persistence {
		# !! is overwritten below !!
		storagePid = {$module.tx_rkwsurvey_evaluation.persistence.storagePid}
	}
	view {
		templateRootPaths.0 = EXT:rkw_survey/Resources/Private/Backend/Templates/
		templateRootPaths.1 = {$module.tx_rkwsurvey_evaluation.view.templateRootPath}
		partialRootPaths.0 = EXT:rkw_survey/Resources/Private/Backend/Partials/
		partialRootPaths.1 = {$module.tx_rkwsurvey_evaluation.view.partialRootPath}
		layoutRootPaths.0 = EXT:rkw_survey/Resources/Private/Backend/Layouts/
		layoutRootPaths.1 = {$module.tx_rkwsurvey_evaluation.view.layoutRootPath}
	}
}

module.tx_rkwsurvey_web_rkwsurveyevaluation.persistence < plugin.tx_rkwsurvey_survey.persistence
