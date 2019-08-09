-- This query returns all answers of a survey, ordered by participant and question uid

-- Set survey uid here
SET @survey = 1;
--

SELECT
  tx_rkwsurvey_domain_model_question.uid, tx_rkwsurvey_domain_model_question.question, tx_rkwsurvey_domain_model_question.required, tx_rkwsurvey_domain_model_question.answer_option,
  tx_rkwsurvey_domain_model_questionresult.survey_result, tx_rkwsurvey_domain_model_questionresult.answer, tx_rkwsurvey_domain_model_questionresult.skipped, DATE_FORMAT(FROM_UNIXTIME(tx_rkwsurvey_domain_model_questionresult.crdate), '%d. %m. %Y %H:%i')  as 'createDate'
FROM `tx_rkwsurvey_domain_model_question`
  LEFT JOIN  `tx_rkwsurvey_domain_model_questionresult`
    ON tx_rkwsurvey_domain_model_questionresult.question = tx_rkwsurvey_domain_model_question.uid
WHERE tx_rkwsurvey_domain_model_question.survey = @survey
  AND tx_rkwsurvey_domain_model_question.deleted = 0
  AND tx_rkwsurvey_domain_model_question.hidden = 0
ORDER BY
  tx_rkwsurvey_domain_model_questionresult.survey_result,
  tx_rkwsurvey_domain_model_question.uid